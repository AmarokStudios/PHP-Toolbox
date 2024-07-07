<?php

namespace DBHelix\Database;

class PerformanceMonitor {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function getSlowQueries($threshold) {
        $sql = "SELECT * FROM information_schema.processlist WHERE time > ?";
        return $this->database->query($sql, [$threshold]);
    }

    public function getQueryExecutionTimes() {
        $sql = "SHOW PROFILES";
        return $this->database->query($sql);
    }
}
?>
