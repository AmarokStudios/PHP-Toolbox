<?php

namespace DBHelix\Database;

class TransactionManager {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function beginTransaction() {
        $this->database->execute('START TRANSACTION');
    }

    public function commit() {
        $this->database->execute('COMMIT');
    }

    public function rollback() {
        $this->database->execute('ROLLBACK');
    }
}
?>
