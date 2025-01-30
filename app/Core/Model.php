<?php

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

abstract class Model
{
    protected PDO $db;

    /**
     * Constructor with Dependency Injection for better testability.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Executes a prepared query.
     */
    public function query(string $sql, array $params = []): ?PDOStatement
    {
        try {
            $stmt = $this->db->prepare($sql);
            error_log('Executing query: ' . $this->maskQuery($sql, $params));

            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Database query error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetches all results from a query.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)?->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    /**
     * Fetches a single result from a query.
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        return $this->query($sql, $params)?->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Masks query parameters for logging.
     */
    private function maskQuery(string $sql, array $params): string
    {
        foreach ($params as $key => $value) {
            $replacement = is_numeric($value) ? '[NUMERIC_PARAM]' : '[STRING_PARAM]';
            $placeholder = is_int($key) ? '?' : ":$key";
            $sql = preg_replace('/' . preg_quote($placeholder, '/') . '/', $replacement, $sql, 1);
        }
        return $sql;
    }
}
