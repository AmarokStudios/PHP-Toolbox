<?php

namespace DBHelix\Database;

class ETLManager {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function extract($query) {
        return $this->database->query($query);
    }

    public function transform($data, $callback) {
        return array_map($callback, $data);
    }

    public function load($tableName, $data) {
        foreach ($data as $row) {
            $columns = implode(', ', array_keys($row));
            $placeholders = implode(', ', array_fill(0, count($row), '?'));
            $sql = "INSERT INTO $tableName ($columns) VALUES ($placeholders)";
            $this->database->execute($sql, array_values($row));
        }
    }
}
?>
