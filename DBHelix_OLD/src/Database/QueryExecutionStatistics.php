<?php

namespace DBHelix\Database;

class QueryExecutionStatistics {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function logExecutionTime($query, $executionTime) {
        $sql = "INSERT INTO query_stats (query, execution_time, timestamp) VALUES (?, ?, NOW())";
        $this->database->execute($sql, [$query, $executionTime]);
    }

    public function getExecutionStats() {
        $sql = "SELECT * FROM query_stats ORDER BY timestamp DESC";
        return $this->database->query($sql);
    }
}
?>
