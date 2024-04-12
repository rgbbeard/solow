<?php
namespace View;

use Service\HTTPService;
use View\BaseViewInterface;

class BaseView implements BaseViewInterface {
	protected string $http_template = "templates/public/http.php";

	protected string $css = "";
	protected string $js = "";
	protected string $img = "";

	protected array $children = [];

	public function __construct() {
		$this->prepare_links();
	}

	public function list_child_classes() {
	    foreach (get_declared_classes() as $class) {
	        if (is_subclass_of($class, __CLASS__)) {
	            $this->children[] = $class;
	        }
	    }
	}

	public function find_owner_of(string $method) {
        if(!empty($this->children)) {
        	foreach($this->children as $child) {
        		if(method_exists($child, $method)) {
        			return $child;
        		}
        	}
        }
        
        return null;
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

	protected function render(string $template_name, array $params = []) {
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