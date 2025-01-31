<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Database;
use PDO;
use PDOStatement;
use PDOException;
use RuntimeException;

abstract class Model {
    protected PDO $db;

    public function __construct() {
        $dbInstance = Database::getInstance(DB_HOST, DB_NAME, DB_USER, DB_PASS);
        $this->db = $dbInstance->getConnection();
    }

    /**
     * Executes a database query with optional parameters.
     */
    public function query(string $query, array $params = []): ?PDOStatement {
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            error_log('Executed query: ' . $this->maskQuery($query, $params));
            return $stmt;
        } catch (PDOException $e) {
            error_log('Database query error: ' . $e->getMessage());
            throw new RuntimeException('Database operation failed.');
        }
    }

    /**
     * Fetch all results as an associative array.
     */
    public function fetchAll(string $query, array $params = []): array {
        return $this->query($query, $params)?->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    /**
     * Fetch a single result as an associative array.
     */
    public function fetch(string $query, array $params = []): ?array {
        return $this->query($query, $params)?->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Masks query parameters for secure logging.
     */
    private function maskQuery(string $query, array $params): string {
        $maskedQuery = $query;
        foreach ($params as $key => $value) {
            $placeholder = is_int($key) ? '?' : ':' . $key;
            $replacement = is_numeric($value) ? '[NUMERIC_PARAM]' : '[STRING_PARAM]';
            $maskedQuery = preg_replace('/' . preg_quote($placeholder, '/') . '/', $replacement, $maskedQuery, 1);
        }
        return $maskedQuery;
    }
}