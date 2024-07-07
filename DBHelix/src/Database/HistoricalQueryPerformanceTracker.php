<?php

namespace DBHelix\Database;

class HistoricalQueryPerformanceTracker {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function logQueryPerformance($query, $executionTime) {
        $sql = "INSERT INTO query_performance_log (query, execution_time, timestamp) VALUES (?, ?, NOW())";
        $this->database->execute($sql, [$query, $executionTime]);
    }

    public function getPerformanceHistory() {
        $sql = "SELECT * FROM query_performance_log";
        return $this->database->query($sql);
    }
}
?>
