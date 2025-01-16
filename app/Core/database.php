<?php

namespace App\Core;

use PDO;
use PDOException;

// Database credentials
define('DB_HOST', 'postgres');
define('DB_NAME', 'xml_test');
define('DB_USER', 'test');
define('DB_PASS', 'secret');

abstract class Database {
	public static ?PDO $pdo = null;

	public function __construct() {
		if (self::$pdo === null) {
			try {
				$dsn = "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME;
				self::$pdo = new PDO($dsn, DB_USER, DB_PASS);
				self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
				die("Database connection failed: " . $e->getMessage());
			}
		}
	}
}