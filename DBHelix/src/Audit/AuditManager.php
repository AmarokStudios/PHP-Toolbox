<?php

namespace DBHelix\Audit;

class AuditManager {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function logChange($tableName, $operation, $data) {
        $sql = "INSERT INTO audit_log (table_name, operation, data, timestamp) VALUES (?, ?, ?, NOW())";
        $this->database->execute($sql, [$tableName, $operation, json_encode($data)]);
    }

    public function getChanges($tableName) {
        $sql = "SELECT * FROM audit_log WHERE table_name = ?";
        return $this->database->query($sql, [$tableName]);
    }
}
?>
