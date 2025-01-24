<?php
namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct($host, $dbname, $username, $password) {
        try {
            $options = [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                PDO::ATTR_PERSISTENT => true
            ];

            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                $options
            );

        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new RuntimeException('Database connection error.');
        }
    }

    public static function getInstance($host, $dbname, $username, $password) {
        if (self::$instance === null) {
            self::$instance = new self($host, $dbname, $username, $password);
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
