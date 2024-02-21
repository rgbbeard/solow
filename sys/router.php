<?php

namespace Router;

use Session\SessionManager;
use View\ContentView;
use Service\HTTPService;
use \Exception;

SessionManager::check_session();

class Router {
	/**
	 * These entries are not used in the menu
	 * they serve as a reference to the
	 * content functions
	 */
	protected const routes_path = "sys/config/routes.ini";
	protected static ?array $routes = null;
	protected static string $current_route = "";

	protected ContentView $contentView;

	public function __construct($path) {
		$this->contentView = new ContentView();
		$params = [];
		
		if(self::has_get_data()) {
			$params = self::get_get_data();
		} elseif(self::has_post_data()) {
			$params = self::get_post_data();
		}

		$this->watch($path, $params);
	}

	# A POST request has been sent
	public static function has_post_data() {
		return $_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST);
	}

	# A GET request has been sent
	public static function has_get_data() {
		return $_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET);
	}

	public function route_has_params(string $route_name) {
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
	 * requests parameters values are sent
	 * without their associated name
	 */
	protected static function convert_params(array $params): array
	{
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
	public static function has_required_roles(array $roles): bool
	{
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

	/**
	 * Check for the permission to view
	 * the requested page and renders it
	 */
	public function goto(string $route_name, array $params = []) {
		$route = self::$routes[$route_name];

		if(@!empty($route["scripts"])) {
			$params["scripts"] = $route["scripts"];
		}

		# User must be authenticated
		if(!empty($route["authenticated"]) && !self::has_required_roles($route["roles"])) {
			info_log("Authentication needed");
			$path = "";
			$auth_route = self::$routes[$route["authentication"]];

			if(self::$current_route != $route_name) {
				info_log("Getting login route for: $route_name");

				if(defined("localhost_base") && !empty(localhost_base)) {
					$path .= "/" . localhost_base;
				}

				$path .= $auth_route["path"];

				if(!headers_sent()) {
					info_log("Going to the authentication page");

					# Goto the authentication page
					header("Location: $path");
					die();
				} else {
					die("A request has already been sent.");
				}
			}
		}

		info_log("Calling view: " . $route["function"]);

		self::$current_route = $route_name;

		if($this->route_has_params($route_name)) {
			$params = $this->extract_url_params($route_name);
		}

		# Reset params
		$p = [];
		$p["params"] = json_encode($params);

		call_user_func_array([
			$this->contentView,
			$route["function"]
		], array_values($p));
	}

	public function watch($path_info, array $params) {
		$match = false;

		if(defined("localhost_base") && !empty(localhost_base)) {
			array_shift($path_info);
		}

		$path_info = implode("/", $path_info);

		try {
			foreach(self::$routes as $name => $route) {
				switch($route["match_type"]) {
					case "regex":
						if(preg_match("/" . $route["path"] . "/", $path_info)) {
							if (!empty($route["params"]) && !empty($params)) {
								
							} else {
								$match = true;
								$this->goto($name, $params);
							}
						}
						break;
					case "plain":
						if($path_info === $route["path"]) {
							$match = true;
							$this->goto($name, $params);
						}
						break;
				}
			}
		} catch(Exception $ignore) {}

		if(!$match && http_response_code() !== 400) {
			# No route found
			HTTPService::set_404_header();
		}

		# Catch http response
		if(http_response_code() !== 200) {
			# Display server error page
			$this->contentView->render_http_response(
				new HTTPService(http_response_code())
			);
		}
	}

	public static function get_current_route(): string
	{
		return self::$current_route;
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
					$link .= "$name";

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

	private function extract_url_params(string $route_name): array {
	    # parse the url
	    $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
	    $path_segments = explode('/', trim($path, '/'));


	    # remove localhost
	    if(defined("localhost_base")) {
	    	$path_segments = array_exclude($path_segments, 0);
	    }

	    foreach(self::$routes as $name => $route) {
	        if($name === $route_name) {
			    if($path_segments[0] == $route["path"]) {
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