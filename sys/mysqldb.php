<?php
namespace Database;

class MySQL {
	protected ?\PDO $connection = null;
	protected static bool $localhost = false;
	protected ?\PDOStatement $prepare = null;
	public array $result = [];
	public int $rows = 0;

	public function __construct(
		string $hostname = "127.0.0.1",
		string $username = "username",
		string $password = "password",
		string $dbname = "dbname",
		string $port = "3306"
	) {
		if(empty($this->connection) || !($this->connection instanceof \PDO)) {
			if(self::$localhost) {
				$hostname = "127.0.0.1";
				$username = "root";
				$password = "mysql";
				$dbname = "dbname";
			}
			return $this->connect($hostname, $username, $password, $dbname, $port);
		}
		return $this->connection;
	}

	public function __destruct() {
		# No need to close connection manually
		$this->connection = null;
	}

	public static function is_localhost(bool $localhost = false) {
		self::$localhost = $localhost;
	}

	public function is_connected(): bool {
		return ($this->connection instanceof \PDO);
	}

	protected function connect(
		string $hostname,
		string $username,
		string $password,
		string $dbname,
		string $port
	): ?\PDO {
		# No need to open connection manually
		try {
			$this->connection = new \PDO("mysql:host=$hostname;dbname=$dbname;port=$port", $username, $password);
		} catch(\PDOException $ce) {
			print_r($ce->getMessage());
		}
		return $this->connection;
	}

	public function execute(string $query): bool {
		if(!empty($query)) {
			try {
				$this->clear();
				$this->prepare = $this->connection->prepare($query);
				$sql_exec = $this->prepare->execute();
				if($sql_exec) {
					$this->get_rows();
					if($this->rows > 0) {
						while($sql_result = $this->get_result()) {
							$this->result[] = $sql_result;
						}
					}
				}
				return $sql_exec;
			} catch(\Exception $e) {
				print_r($e->getMessage());
			}
		}
		return false;
	}

	public function clear() {
		$this->prepare = null;
		$this->result = [];
		$this->rows = 0;
	}

	public function get_rows(): int {
		$this->rows = $this->prepare->rowCount();
		return $this->rows;
	}

	public function get_result() {
		return $this->prepare->fetch(\PDO::FETCH_ASSOC);
	}
}