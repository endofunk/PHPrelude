<?php
namespace Endofunk\Lib;

use Endofunk\Core\Module;
use Endofunk\Data\Result;
use Exception;
use PDO;

class SQL extends Module {

	protected static function __connect(string $dsn, string $database, string $host, string $username, string $password): Result {
		return Result::try (function () use ($dsn, $database, $host, $username, $password) {
			return new PDO("$dsn:dbname=$database;host=$host", $username, $password);
		});
	}

	protected static function __query($sql): callable {
		return function ($conn) use ($sql) {
			return Result::try (function () use ($conn, $sql) {
				$result = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
				if (!$result) {
					throw new Exception($mysqli->error0, $severity, $file, $line);
				}
				return [$conn, $result];
			});
		};
	}

	protected static function __close(): callable {
		return function ($conn) {
			return Result::try (function () use ($conn) {
				$conn[0] = null;
				return $conn[1];
			});
		};
	}
}
?>