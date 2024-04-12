<?php
namespace Session;

use Database\MySQL;
use Exceptions\InvalidSessionException;

class SessionManager {
	protected MySQL $conn;

	public function __construct() {
		$this->conn = new MySQL();
	}
	
	/**
	 * @throws InvalidSessionException
	 */
	public static function check_session(): string {
		switch(session_status()) {
			case PHP_SESSION_DISABLED:
				throw new InvalidSessionException("Sessions are disabled");
			case PHP_SESSION_NONE:
				session_start();
				break;
			case PHP_SESSION_ACTIVE:
				return session_id();
		}
		
		return session_id();
	}
	
	/**
	 * @throws InvalidSessionException
	 */
	public static function reset_session() {
		# an existing session was found
		if(self::check_session()) {
			$_SESSION = [];
		}
	}

	public static function restart_session() {
		session_destroy();
		session_start();
	}
}