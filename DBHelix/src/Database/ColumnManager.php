<?php

namespace DBHelix\Database;

class ColumnManager {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function addColumn($tableName, $columnName, $columnType) {
        // Implementation for adding a column
        $sql = "ALTER TABLE $tableName ADD $columnName $columnType";
        return $this->database->execute($sql);
    }

    public function dropColumn($tableName, $columnName) {
        // Implementation for dropping a column
        $sql = "ALTER TABLE $tableName DROP COLUMN $columnName";
        return $this->database->execute($sql);
    }

    public function modifyColumn($tableName, $columnName, $newType) {
        // Implementation for modifying a column
        $sql = "ALTER TABLE $tableName MODIFY COLUMN $columnName $newType";
        return $this->database->execute($sql);
    }

    public function renameColumn($tableName, $oldName, $newName, $newType) {
        // Implementation for renaming a column
        $sql = "ALTER TABLE $tableName CHANGE $oldName $newName $newType";
        return $this->database->execute($sql);
    }

    public function addConstraint($tableName, $constraint) {
        // Implementation for adding a constraint
        $sql = "ALTER TABLE $tableName ADD CONSTRAINT $constraint";
        return $this->database->execute($sql);
    }

    public function dropConstraint($tableName, $constraintName) {
        // Implementation for dropping a constraint
        $sql = "ALTER TABLE $tableName DROP CONSTRAINT $constraintName";
        return $this->database->execute($sql);
    }
}
?>
