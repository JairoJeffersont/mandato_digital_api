<?php

namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Base Model Class
 * 
 * This abstract class serves as the foundation for all models in the application.
 * It provides common database operations and utility methods that can be inherited
 * by specific model classes.
 *
 * Features:
 * - Database connection management
 * - Basic CRUD operations
 * - Flexible column-based queries
 *
 * @package App\Models
 * @version 1.0.0
 */
abstract class BaseModel {
    /**
     * PDO database connection instance
     *
     * @var PDO
     */
    protected $db;

    /**
     * Name of the database table associated with the model
     *
     * @var string
     */
    protected $table;

    /**
     * Constructor
     * 
     * Initializes the database connection using the singleton pattern
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Creates a new record in the database
     *
     * @param array $data Associative array of column names and values to insert
     * @return void
     */
    public function create(array $data): void {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
    }

    /**
     * Retrieves all records from the table
     *
     * @return array Array of all records from the table
     */
    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    /**
     * Retrieves all records from the table by a specific column value
     *
     * @param string $column The column name to search in
     * @param mixed  $value  The value to search for
     * @return array Array of all records from the table
     */
    public function getAllByColumn(string $column, mixed $value): array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Finds a single record by a specific column value
     *
     * @param string $column The column name to search in
     * @param mixed  $value  The value to search for
     * @return array|null The found record or null if not found
     */
    public function findOne(string $column, mixed $value): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Deletes records matching a specific column value
     *
     * @param string $column The column name to match for deletion
     * @param mixed  $value  The value to match for deletion
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $column, mixed $value): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$column} = :value");
        $stmt->bindParam(':value', $value);
        return $stmt->execute();
    }

    /**
     * Updates records matching a specific ID column value
     *
     * @param string $idColumn The name of the ID column
     * @param mixed  $idValue  The value of the ID to match
     * @param array  $data     Associative array of column names and new values
     * @return bool True if update was successful, false otherwise
     */
    public function update(string $idColumn, mixed $idValue, array $data): bool {
        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = ?";
        }
        $setString = implode(', ', $setParts);

        $sql = "UPDATE {$this->table} SET {$setString} WHERE {$idColumn} = ?";
        $stmt = $this->db->prepare($sql);

        $values = array_values($data);
        $values[] = $idValue;

        return $stmt->execute($values);
    }
}
