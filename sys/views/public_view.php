<?php
namespace View;

use Router\Router;
use Service\ContentService;
use Service\UserService;
use Service\HTTPService;
use Session\SessionManager;

class PublicView extends BaseView {
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
			"home" => [
				"order" => 1,
				"visible" => true,
				"display" => "Home"
			],
			"services" => [
				"order" => 2,
				"visible" => true,
				"display" => "Servizi"
			],
			"contacts" => [
				"order" => 3,
				"visible" => true,
				"display" => "Contatti"
			]
		];

		return $this->render("templates/public/navbar.php", [
			"menu_items" => $menu_items
		]);
	}

	protected function get_admin_navbar() {
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

	protected function get_footer() {
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
}