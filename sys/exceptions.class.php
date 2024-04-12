<?php 
namespace Exceptions;

use \Exception;

class InvalidSessionException extends Exception {
	public function __construct(string $message) {
		parent::__construct($message);
	}
}

class ControllerNotFoundException extends Exception {
	public function __construct(string $message) {
		parent::__construct($message);
	}
}
