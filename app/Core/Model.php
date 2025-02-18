<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Database;
use PDO;
use PDOStatement;
use PDOException;
use RuntimeException;

abstract class Model {
    /** @var PDO The database connection instance */
    protected PDO $db;

    /**
     * Constructor method to initialize the database connection.
     * This uses the singleton Database instance to establish the connection.
     */
    public function __construct() {
        // Get the database connection instance
        $dbInstance = Database::getInstance(DB_HOST, DB_NAME, DB_USER, DB_PASS);
        $this->db = $dbInstance->getConnection();
    }

    /**
     * Executes a database query with optional parameters.
     *
     * @param string $query The SQL query to execute
     * @param array $params An array of parameters to bind to the query (optional)
     * @return PDOStatement|null The prepared statement object if successful, null on failure
     * @throws RuntimeException If the query execution fails
     */
    public function query(string $query, array $params = []): ?PDOStatement {
        try {
            // Prepare and execute the query with the provided parameters
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            // Log the query execution with masked parameters
            error_log('Executed query: ' . $this->maskQuery($query, $params));
            return $stmt;
        } catch (PDOException $e) {
            // Log the error and throw a RuntimeException
            error_log('Database query error: ' . $e->getMessage());
            throw new RuntimeException('Database operation failed.');
        }
    }

    /**
     * Fetches all results of a query as an associative array.
     *
     * @param string $query The SQL query to execute
     * @param array $params The parameters to bind to the query (optional)
     * @return array An array of results (empty array if no results)
     */
    public function fetchAll(string $query, array $params = []): array {
        // Return all fetched rows as an associative array
        return $this->query($query, $params)?->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    /**
     * Fetches a single result of a query as an associative array.
     *
     * @param string $query The SQL query to execute
     * @param array $params The parameters to bind to the query (optional)
     * @return array|null The result as an associative array, or null if no result
     */
    public function fetch(string $query, array $params = []): ?array {
        // Return a single fetched row as an associative array
        return $this->query($query, $params)?->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Masks query parameters for secure logging by replacing sensitive data with placeholders.
     *
     * @param string $query The SQL query with placeholders
     * @param array $params The parameters that will be bound to the query
     * @return string The query with masked parameters
     */
    private function maskQuery(string $query, array $params): string {
        $maskedQuery = $query;
        foreach ($params as $key => $value) {
            $placeholder = is_int($key) ? '?' : ':' . $key;
            // Mask numeric and string values to prevent sensitive data leakage
            $replacement = is_numeric($value) ? '[NUMERIC_PARAM]' : '[STRING_PARAM]';
            $maskedQuery = preg_replace('/' . preg_quote($placeholder, '/') . '/', $replacement, $maskedQuery, 1);
        }
        return $maskedQuery;
    }
}