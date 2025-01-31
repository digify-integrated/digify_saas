<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database {
    private static array $instances = [];
    private PDO $connection;

    /**
     * Private constructor to enforce Singleton pattern.
     */
    private function __construct(string $host, string $dbname, string $username, string $password) {
        try {
            $this->connection = new PDO(
                dsn: "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                username: $username,
                password: $password,
                options: [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                    PDO::ATTR_PERSISTENT => true
                ]
            );
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new RuntimeException('Database connection error.');
        }
    }

    /**
     * Returns a singleton instance of the Database connection.
     */
    public static function getInstance(
        string $host,
        string $dbname,
        string $username,
        string $password
    ): self {
        $key = md5("{$host}_{$dbname}_{$username}");

        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new self($host, $dbname, $username, $password);
        }

        return self::$instances[$key];
    }

    /**
     * Returns the active PDO connection.
     */
    public function getConnection(): PDO {
        return $this->connection;
    }

    /**
     * Prevent cloning of the instance.
     */
    private function __clone() {}

    /**
     * Prevent unserialization.
     */
    public function __wakeup(): void {
        throw new RuntimeException('Cannot unserialize a singleton.');
    }
}
