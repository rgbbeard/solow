<?php
namespace View;

use Router\Router;
use Service\ContentService;
use Service\UserService;
use Service\HTTPService;
use Session\SessionManager;

class AdminView extends BaseView {
	protected ContentService $contentService;
	protected UserService $userService;
	
	public function __construct() {
		parent::__construct();

		$this->contentService = new ContentService();
		$this->userService = new UserService();
	}

	protected function get_navbar() {
		# TODO: dynamically read menu voices
		$menu_items = [
			"admin_logout" => [
				"order" => 1,
				"visible" => true,
				"display" => "Logout"
			],
			"home" => [
				"order" => 2,
				"visible" => true,
				"display" => "Torna al sito"
			]
		];

		return parent::render("templates/admin/navbar.php", [
			"menu_items" => $menu_items
		]);
	}

	protected function check_login() {
		$allowed = Router::has_required_roles(["ROLE_ADMIN"]);

		if(!$allowed) {
			# access denied
			info_log("Access denied to:" . $_SERVER["HTTP_URI"]);
			$this->render_http_response(new HTTPService(http_response_code(401)));
		}

		return $allowed;
	}

	public function get_admin_home() {
		$navbar = $this->get_navbar();

		echo parent::render("templates/admin/home.php", [
			"navbar" => $navbar
		]);
	}

	public function get_admin_login(?string $params = "") {
		if(Router::has_required_roles(["ROLE_ADMIN"])) {
			Router::redirect_to("admin");
		}

		$navbar = $this->get_navbar();
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

	public function get_admin_manage_contacts() {
		if($this->check_login()) {
			$navbar = $this->get_navbar();

			$contacts = $this->contentService->get_contacts();

			echo parent::render("templates/admin/manage_contacts.php", [
				"navbar" => $navbar,
				"contacts" => $contacts
			]);
		}
	}

	public function exec_admin_add_contact(?string $params = "") {
		if($this->check_login()) {
			$params = json_decode($params);

			# least level of protection against malicious calls
			if($params->add_new_contact && $params->add_new_contact === "Aggiungi") {
				$this->contentService->add_contact(
					$params->type,
					$params->value,
					$_SESSION["USERNAME"]
				);
			}

			Router::redirect_to("admin");
		}
	}

	public function exec_admin_modify_contacts(?string $params = "") {
		if($this->check_login()) {
			var_dump($params);
			Router::redirect_to("admin_manage_contacts");
		}
	}

	public function exec_admin_delete_contact(?string $params = "") {
		if($this->check_login()) {
			$params = json_decode($params);

			if(!@empty($params->id)) {
				$this->contentService->delete_contact_by_id($params->id);
			}

			Router::redirect_to("admin_manage_contacts");
		}
	}

	public function exec_admin_logout() {
		if($this->check_login()) {
			SessionManager::restart_session();
		}

		Router::redirect_to("admin_login");
	}
}