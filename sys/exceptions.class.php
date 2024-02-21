<?php 
namespace Exceptions;

use Exception;

class InvalidSessionException extends Exception {
	/**
	 * @throws Exception
	 */
	public function __construct(string $message) {
		parent::__construct();
		throw new Exception($message);
	}
}
?>