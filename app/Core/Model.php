<?php 
namespace App\Core;

use App\Core\Database;
use PDO;

abstract class Model {
    protected $db;

    public function __construct() {
        $dbInstance = Database::getInstance(
            DB_HOST, 
            DB_NAME, 
            DB_USER, 
            DB_PASS
        );
        
        $this->db = $dbInstance->getConnection();
    }

    public function query(string $query, array $params = []) {
        try {
            $stmt = $this->db->prepare($query);
            error_log('Executing query: ' . $this->maskQuery($query, $params));

            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            error_log('Database query error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function fetchAll(string $query, array $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    
    public function fetch(string $query, array $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    }

    private function maskQuery(string $query, array $params): string {
        foreach ($params as $key => $value) {
            $valuePlaceholder = is_numeric($value) ? '[NUMERIC_PARAM]' : '[STRING_PARAM]';
            $placeholder = is_int($key) ? '?' : ":$key";
            $query = str_replace($placeholder, $valuePlaceholder, $query);
        }
        return $query;
    }


}
?>