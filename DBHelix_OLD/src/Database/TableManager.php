<?php

namespace DBHelix\Database;

class TableManager {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function createTable($tableName, $columns) {
        // Implementation for creating a table
        $columnsSql = implode(', ', array_map(function($name, $type) {
            return "$name $type";
        }, array_keys($columns), $columns));
        
        $sql = "CREATE TABLE $tableName ($columnsSql)";
        return $this->database->execute($sql);
    }

    public function dropTable($tableName) {
        // Implementation for dropping a table
        $sql = "DROP TABLE IF EXISTS $tableName";
        return $this->database->execute($sql);
    }

    public function listTables() {
        // Implementation for listing tables
        $sql = "SHOW TABLES";
        return $this->database->query($sql);
    }

    public function renameTable($oldName, $newName) {
        // Implementation for renaming a table
        $sql = "RENAME TABLE $oldName TO $newName";
        return $this->database->execute($sql);
    }

    public function truncateTable($tableName) {
        // Implementation for truncating a table
        $sql = "TRUNCATE TABLE $tableName";
        return $this->database->execute($sql);
    }
}
?>
