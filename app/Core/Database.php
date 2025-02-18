<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database {
    /** @var array Holds the instances of the Database connection for each unique configuration */
    private static array $instances = [];

    /** @var PDO The PDO connection instance */
    private PDO $connection;

    /**
     * Private constructor to enforce Singleton pattern.
     * Initializes a new PDO connection to the database.
     *
     * @param string $host The database host
     * @param string $dbname The database name
     * @param string $username The database username
     * @param string $password The database password
     * @throws RuntimeException If the database connection fails
     */
    private function __construct(string $host, string $dbname, string $username, string $password) {
        try {
            $this->connection = new PDO(
                "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                    PDO::ATTR_PERSISTENT => true
                ]
            );
        } catch (PDOException $e) {
            // Log the error and throw a more generic exception for security
            error_log('Database connection failed: ' . $e->getMessage());
            throw new RuntimeException('Database connection error.');
        }
    }

    /**
     * Returns a singleton instance of the Database connection.
     * If the connection already exists, it returns the existing instance.
     *
     * @param string $host The database host
     * @param string $dbname The database name
     * @param string $username The database username
     * @param string $password The database password
     * @return self The singleton instance of the Database class
     */
    public static function getInstance(
        string $host,
        string $dbname,
        string $username,
        string $password
    ): self {
        // Create a unique key for the database connection based on provided credentials
        $key = md5("{$host}_{$dbname}_{$username}");

        // Create a new connection if one doesn't exist already for the given credentials
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new self($host, $dbname, $username, $password);
        }

        return self::$instances[$key];
    }

    /**
     * Returns the active PDO connection instance.
     *
     * @return PDO The active PDO connection
     */
    public function getConnection(): PDO {
        return $this->connection;
    }

    /**
     * Prevent cloning of the singleton instance.
     */
    private function __clone() {}

    /**
     * Prevent unserialization of the singleton instance.
     * 
     * @throws RuntimeException If attempting to unserialize the singleton instance
     */
    public function __wakeup(): void {
        throw new RuntimeException('Cannot unserialize a singleton.');
    }
}
