<?php

namespace Router;

use Exceptions\InvalidSessionException;
use Session\SessionManager;
use View\BaseView;
use View\PublicView;
use Service\HTTPService;
use \Exception;
use Exceptions\ControllerNotFoundException;

try {
	SessionManager::check_session();
} catch (InvalidSessionException $ise) {
	echo $ise->getMessage();
}

class Router {
	/**
	 * These entries are not used in the menu
	 * they serve as a reference to the
	 * content functions
	 */
	protected const routes_path = "sys/config/routes.ini";
	protected static ?array $routes = null;
	/** 
	 * Setting the default selected route
	 * used in navigation to highlight
	 * the current viewed page
	 */
	public static string $current_route = "home";

	protected BaseView $baseView;
	protected PublicView $publicView;

	public function __construct($path) {
		$this->baseView = new BaseView();
		$this->publicView = new PublicView();

		$this->baseView->list_child_classes();

		$params = [];
		
		if(self::has_get_data()) {
			$params = self::get_get_data();
		} elseif(self::has_post_data()) {
			$params = self::get_post_data();
		}

		$this->watch($path, $params);
	}

	public static function get_current_route(): string {
		return self::$current_route;
	}

	# A POST request has been sent
	public static function has_post_data(): bool {
		return $_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST);
	}

	# A GET request has been sent
	public static function has_get_data(): bool {
		return $_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET);
	}

	public function route_has_params(string $route_name): bool {
		return !@empty(self::$routes[$route_name]) 
			&& self::$routes[$route_name]["params"] != false
			&& count(self::$routes[$route_name]["params"]) > 0;
	}

	public static function get_post_data($index = null) {
		$params = self::convert_params($_POST);

		if(!is_null($index)) {
			return $params[$index];
		}

		return $params;
	}

	public static function get_get_data($index = null) {
		$params = self::convert_params($_GET);

		if(!is_null($index)) {
			return $params[$index];
		}

		return $params;
	}

	/**
	 * BUGFIX
	 * request parameters' values are sent
	 * without their associated name
	 */
	protected static function convert_params(array $params): array {
		$tmp = [];

		foreach($params as $name => $value) {
			$tmp[$name] = $value;
		}

		return $tmp;
	}

	/**
	 * Verify that the user has logged in
	 * and has the required roles to view
	 * the requested page
	 */
	public static function has_required_roles(array $roles): bool {
		if(!empty($_SESSION["ROLES"])) {
			foreach($roles as $role) {
				if(!in_array($role, $_SESSION["ROLES"])) {
					return false;
				}
			}
		} else {
			return false;
		}

		return true;
	}

	protected function get_route_by_attr(string $attribute, ?string $value = null): ?string {
		if(empty($value)) {
			return null;
		}

		foreach(self::$routes as $name => $route) {
			if($route[$attribute] === $value) {
				return $name;
			}
		}

		return null;
	}

	/**
	 * Check for the permission to view
	 * the requested page and render it
	 */
	public function goto(string $route_name, string $path_info, array $params = []) {
		$route = self::$routes[$route_name];

		if(@!empty($route["scripts"])) {
			$params["scripts"] = $route["scripts"];
		}

		# User must be authenticated
		if(!empty($route["authenticated"]) && !self::has_required_roles($route["roles"])) {
			info_log("Authentication needed");
			$path = "";
			$auth_route = self::$routes[$route["authentication"]];
			
			if(defined("localhost_base") && !empty(localhost_base)) {
				$path .= "/" . localhost_base;

				if(!str_contains($path_info, localhost_base)) {
					$path_info = "/" . localhost_base . $path_info;
				}
			}

			$path .= $auth_route["path"];

			if($path_info !== $path) {
				info_log("Getting login route for: $route_name");


				if(!headers_sent()) {
					info_log("Going to the authentication page");

					# Goto the authentication page
					$auth_route_name = $this->get_route_by_attr("function", $auth_route["function"]);

					if(!empty($auth_route_name)) {
						$route = self::$routes[$auth_route_name];
					}
				} else {
					die("A request has already been sent.");
				}
			}
		}

		info_log("Calling view: " . $route["function"]);
		if($this->route_has_params($route_name)) {
			$params = $this->extract_url_params($route_name);
		}

		# Used for navigation
		self::$current_route = $route_name;

		# Reset params
		$p = [];
		$p["params"] = json_encode($params);

		if(!empty($route) && $route["function"]) {
			try {
				# Dynamically find the correct controller
				$controllerClass = $this->baseView->find_owner_of($route["function"]);
				$controllerInstance = new $controllerClass();

				call_user_func_array([
					$controllerInstance,
					$route["function"]
				], array_values($p));
			} catch(ControllerNotFoundException $e) {
				info_log($e->getMessage(), 2);
			}
		}
	}

	public function watch($path_info, array $params) {
		$match = false;

		if(defined("localhost_base") && !empty(localhost_base)) {
			array_shift($path_info);
		}

		$path_info = implode("/", $path_info);

		try {
			$name = "home";
			$route = self::$routes[$name];

			# no specific path given, going home
			if(!empty($path_info)) {
				foreach(self::$routes as $n => $r) {
					$regex = str_replace("/", "\/", $r["path"]);

					if(preg_match("/^$regex/", $path_info)) {
						if(empty($r["path"])) {
							continue;
						}

						$path_diff = str_replace($r["path"], "", $path_info);
						$path_diff = preg_replace("/^\//", "", $path_diff);
						$path_params = array_clear(explode("/", $path_diff));

						if($r["match_type"] === "regex") {
							if(count($path_params) === count($r["params"])) {
								$name = $n;
								$route = $r;
								break;
							}
						} else {
							if(empty($path_diff)) {
								$name = $n;
								$route = $r;
								break;
							}
						}
					}
				}
			}

			if(!empty($name) && !empty($route)) {
				$match = true;
				
				if($route["match_type"] === "regex") {
					$params = $this->extract_url_params($name);
				}

				info_log("Matched route name: $name");

				$this->goto($name, $path_info, $params);
			} else {
				info_log("No route matched, going 404..", 3);
			}
		} catch(Exception $ignore) {}

		if(!$match && http_response_code() !== 400) {
			# No route found
			HTTPService::set_404_header();
		}

		# Catch http response
		if(http_response_code() !== 200) {
			# Display server error page
			$this->publicView->render_http_response(
				new HTTPService(http_response_code())
			);
		}
	}

	public static function set_routes() {
		self::$routes = parse_ini_file(self::routes_path, true);
	}

	public static function generate_link(string $route_name, array $params = []): string {
		$link = "";

		if(defined("localhost_base") && !empty(localhost_base)) {
			$link .= "/" . localhost_base;
		}

		foreach(self::$routes as $name => $route) {
			if($name === $route_name) {
				if($route["match_type"] === "plain" && @empty($route["params"])) {
					$link .= $route["path"];
					break;
				} elseif(($route["match_type"] === "plain" || $route["match_type"] === "regex") 
					&& !@empty($route["params"]) && !@empty($params)) {
					$link .= $route["path"];

					try {
						$expr = implode("/", $route["params"]);
						$link .= sprintf("/$expr", ...$params);
					} catch(Exception $ignore) {}
					break;
				}
			}
		}

		return $link;
	}

	private function extract_url_params(string $route_name, ?string $url = null): array {
	    # parse the url
	    $path = parse_url($url ?? $_SERVER["REQUEST_URI"], PHP_URL_PATH);
	    $path_segments = explode("/", trim($path, "/"));

	    # remove localhost
	    if(defined("localhost_base")) {
	    	$path_segments = array_exclude($path_segments, 0);
	    }

	    foreach(self::$routes as $name => $route) {
	        if($name === $route_name) {
	        	if(str_contains($route["path"], "/")) {
	        		foreach(explode("/", trim($route["path"], "/")) as $rp) {
	        			foreach($path_segments as $index => $ps) {
	        				if($ps == $rp) {
							    $path_segments = array_exclude($path_segments, $index);
				    		}
	        			}
	        		}
	        	} elseif($path_segments[0] == $route["path"]) {
				    $path_segments = array_exclude($path_segments, 0);
	    		}

	    		$tmp = [];

	    		foreach($path_segments as $parameter) {
	    			foreach($route["params"] as $name => $p) {
	    				if($match = sscanf($parameter, $p)) {
	    					$tmp[$name] = $match[0];
	    				}
	    			}
	    		}

	    		return $tmp;
	        }
	    }

	    return [];
	}

	public static function redirect_to(string $route_name, array $params = []) {
		$link = self::generate_link($route_name, $params);
		header("Location: $link");
	}
}