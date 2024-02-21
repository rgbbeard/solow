<?php
namespace View;

use Router\Router;
use Service\ContentService;
use Service\UserService;
use Service\HTTPService;
use Session\SessionManager;

class BaseView {
	protected string $http_template = "templates/public/http.php";

	protected string $css = "";
	protected string $js = "";
	protected string $img = "";

	protected function __construct() {
		$this->prepare_links();
	}

	public function prepare_links() {
		if(empty($this->css) || empty($this->js) || empty($this->img)) {
			$this->css .= "res/css/";
			$this->js .= "res/js/";
			$this->img .= "res/img/";
		}

		if(defined("localhost_base") && !empty(localhost_base)) {
			if(!str_contains($this->css, localhost_base) 
				|| !str_contains($this->js, localhost_base) 
				|| !str_contains($this->img, localhost_base)) {
				$this->css = "/" . localhost_base . $this->css;
				$this->js = "/" . localhost_base . $this->js;
				$this->img = "/" . localhost_base . $this->img;
			}
		}
	}

	protected function render(string $template_name, array $params = []): bool|string
	{
		ob_start();
		$this->prepare_links();

		$params = array_merge([
			"img" => $this->img,
			"css" => $this->css,
			"js" => $this->js
		], $params);

		if(file_exists($template_name)) {
			require_once $template_name;
		} else {
			$template_name = $this->http_template;
			if(file_exists($template_name)) {
				require_once $template_name;
			}
		}

		return ob_get_clean();
	}

	public function render_http_response(HTTPService $response) {
		$navbar = $this->get_navbar();

		echo $this->render(
			$this->http_template, [
				"response" => $response, 
				"title" => $response->httpr->message,
				"navbar" => $navbar
			]
		);
	}
}

class ContentView extends BaseView {
	protected ContentService $contentService;
	protected UserService $userService;
	
	public function __construct() {
		parent::__construct();

		$this->contentService = new ContentService();
		$this->userService = new UserService();
	}

	# show debugs
	public function __destruct() {
		# Only dev environment
	    if (!empty($_SESSION["DEBUG"]) && get_ip() === '127.0.0.1') {
	        $section = "<section id='debugger-bar'>
	            <button class='btn btn-flat'>Show debugger</button>
	        </section>";
	        $debugger = "<pre id='debugger-content'>
	            <h2>DEBUGGER</h2>";
	        
	        # Get the last debug sent by timestamp	        
            foreach (end($_SESSION["DEBUG"]) as $var) {
                $debugger .= print_r($var, true) . "\n";
            }

	        $debugger .= "</pre>";
	        $script = "<script src='" . $this->js . "debugger.js'></script>";

	        echo $section . $debugger . $script;

	        /**
	         * BUGFIX
	         * this session variable would persist
	         */
	        unset($_SESSION["DEBUG"]);
	    }
	}

	protected function get_navbar(): bool|string
	{
		# TODO: dynamically read menu voices
		$menu_items = [
			"home" => [
				"order" => 1,
				"visible" => true,
				"display" => "Home",
				"link" => "home"
			],
			"services" => [
				"order" => 2,
				"visible" => true,
				"display" => "Servizi",
				"link" => "services"
			],
			"contacts" => [
				"order" => 3,
				"visible" => true,
				"display" => "Contatti",
				"link" => "contacts"
			]
		];

		return $this->render("templates/public/navbar.php", [
			"menu_items" => $menu_items
		]);
	}

	protected function get_admin_navbar(): bool|string
	{
		# TODO: dynamically read menu voices
		$menu_items = [
			"logout" => [
				"order" => 1,
				"visible" => true,
				"display" => "Logout",
				"link" => "admin_logout"
			],
			"return" => [
				"order" => 2,
				"visible" => true,
				"display" => "Torna al sito",
				"link" => "home"
			]
		];

		return parent::render("templates/admin/navbar.php", [
			"menu_items" => $menu_items
		]);
	}

	protected function get_footer(): bool|string
	{
		$menu_items = [];

		return parent::render("templates/public/footer.php", [
			"menu_items" => $menu_items
		]);
	}

	public function get_home() {
		$navbar = $this->get_navbar();

		$services = $this->contentService->get_services();
		$socials = $this->contentService->get_socials();

		$module_services = parent::render("templates/modules/services.php", [
			"services" => $services
		]);

		# TODO: make dynamic image and caption
		$module_jumbotron = parent::render("templates/modules/jumbotron.php");

		$module_contacts = parent::render("templates/modules/contacts.php", [
			"socials" => $socials
		]);

		echo parent::render("templates/public/home.php", [
			"navbar" => $navbar,
			"module_services" => $module_services,
			"module_jumbotron" => $module_jumbotron,
			"module_contacts" => $module_contacts
		]);
	}

	public function get_services() {
		$navbar = $this->get_navbar();
		$services = $this->contentService->get_services();

		echo parent::render("templates/public/services.php", [
			"navbar" => $navbar,
			"services" => $services
		]);
	}

	public function get_service(?string $params) {
		$id = 0;

		if(!empty($params)) {
			$params = json_decode($params);
		}

		if(isset($params->id)) {
			$id = $params->id;
		}

		if(!is_numeric($id)) {
			info_log("service id: $id", 3); # Debug
			return;
		}
		
		$navbar = $this->get_navbar();

		$service = $this->contentService->get_service_by_id($id);

		echo parent::render("templates/public/service.php", [
			"navbar" => $navbar,
			"service" => $service
		]);
	}

	public function get_contacts() {
		$navbar = $this->get_navbar();

		echo parent::render("templates/public/contacts.php", [
			"navbar" => $navbar
		]);
	}

	public function exec_contacts_submit() {
		if(Router::has_post_data()) {
			dump(Router::get_post_data());
			die();
		}
	}

	public function get_admin_home() {
		$navbar = $this->get_admin_navbar();

		echo parent::render("templates/admin/home.php", [
			"navbar" => $navbar
		]);
	}

	public function get_admin_login(?string $params = "") {
		if(Router::has_required_roles(["ROLE_ADMIN"])) {
			Router::redirect_to("admin");
		}

		$navbar = $this->get_admin_navbar();
		$success = false;
		$form_sent = false;
		$username = "";
		$password = "";

		if(Router::has_post_data()) {
			$params = json_decode($params);
			$username = $params->username;
			$password = $params->password;

			$success = $this->userService->do_admin_login($username, $password);

			if($success) {
				$_SESSION["ROLES"] = ["ROLE_ADMIN"];
				$_SESSION["USERNAME"] = $username;
				Router::redirect_to("admin");
			}

			$form_sent = true;
		}

		echo parent::render("templates/admin/login.php", [
			"navbar" => $navbar,
			"login_succeeded" => $success,
			"form_sent" => $form_sent,
			"username" => $username,
			"password" => $password
		]);
	}

	public function exec_admin_logout() {
		if(Router::has_required_roles(["ROLE_ADMIN"])) {
			SessionManager::restart_session();
			Router::redirect_to("home");
		} else {
			# access denied
			$this->render_http_response(new HTTPService(http_response_code(401)));
		}
	}
}