<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?self $instance = null;
    private PDO $connection;
    
    // Database credentials should ideally be loaded from an environment file
    private static string $host;
    private static string $dbname;
    private static string $username;
    private static string $password;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
        try {
            $options = [
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                PDO::ATTR_PERSISTENT         => true
            ];

            $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8mb4", self::$host, self::$dbname);
            $this->connection = new PDO($dsn, self::$username, self::$password, $options);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new RuntimeException('Database connection error.');
        }
    }

    /**
     * Initializes database credentials.
     */
    public static function configure(string $host, string $dbname, string $username, string $password): void
    {
        self::$host = $host;
        self::$dbname = $dbname;
        self::$username = $username;
        self::$password = $password;
    }

    /**
     * Returns the singleton instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            if (!isset(self::$host, self::$dbname, self::$username, self::$password)) {
                throw new RuntimeException('Database credentials not configured. Call Database::configure() first.');
            }
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns the PDO connection.
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
