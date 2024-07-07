<?php

namespace DBHelix\Database;

class IndexManager {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function createIndex($tableName, $indexName, $columns) {
        // Implementation for creating an index
        $columnsSql = implode(', ', $columns);
        $sql = "CREATE INDEX $indexName ON $tableName ($columnsSql)";
        return $this->database->execute($sql);
    }

    public function dropIndex($tableName, $indexName) {
        // Implementation for dropping an index
        $sql = "DROP INDEX $indexName ON $tableName";
        return $this->database->execute($sql);
    }

    public function listIndexes($tableName) {
        // Implementation for listing indexes
        $sql = "SHOW INDEXES FROM $tableName";
        return $this->database->query($sql);
    }
}
?>
