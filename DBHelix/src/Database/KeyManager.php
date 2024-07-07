<?php

namespace DBHelix\Database;

class KeyManager {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function addPrimaryKey($tableName, $columns) {
        // Implementation for adding a primary key
        $columnsSql = implode(', ', $columns);
        $sql = "ALTER TABLE $tableName ADD PRIMARY KEY ($columnsSql)";
        return $this->database->execute($sql);
    }

    public function dropPrimaryKey($tableName) {
        // Implementation for dropping a primary key
        $sql = "ALTER TABLE $tableName DROP PRIMARY KEY";
        return $this->database->execute($sql);
    }

    public function addForeignKey($tableName, $keyName, $columns, $referencedTable, $referencedColumns) {
        // Implementation for adding a foreign key
        $columnsSql = implode(', ', $columns);
        $referencedColumnsSql = implode(', ', $referencedColumns);
        $sql = "ALTER TABLE $tableName ADD CONSTRAINT $keyName FOREIGN KEY ($columnsSql) REFERENCES $referencedTable ($referencedColumnsSql)";
        return $this->database->execute($sql);
    }

    public function dropForeignKey($tableName, $keyName) {
        // Implementation for dropping a foreign key
        $sql = "ALTER TABLE $tableName DROP FOREIGN KEY $keyName";
        return $this->database->execute($sql);
    }

    public function addUniqueKey($tableName, $keyName, $columns) {
        // Implementation for adding a unique key
        $columnsSql = implode(', ', $columns);
        $sql = "ALTER TABLE $tableName ADD CONSTRAINT $keyName UNIQUE ($columnsSql)";
        return $this->database->execute($sql);
    }

    public function dropUniqueKey($tableName, $keyName) {
        // Implementation for dropping a unique key
        $sql = "ALTER TABLE $tableName DROP INDEX $keyName";
        return $this->database->execute($sql);
    }
}
?>
