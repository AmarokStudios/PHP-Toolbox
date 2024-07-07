<?php

namespace DBHelix\Database;

class IndexUsageTracker {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function trackUsage($query) {
        $sql = "EXPLAIN " . $query;
        $result = $this->database->query($sql);
        $indexUsed = $result[0]['key'] ?? 'none';

        $sql = "INSERT INTO index_usage (query, index_used, timestamp) VALUES (?, ?, NOW())";
        $this->database->execute($sql, [$query, $indexUsed]);
    }

    public function getIndexUsageStats() {
        $sql = "SELECT * FROM index_usage ORDER BY timestamp DESC";
        return $this->database->query($sql);
    }
}
?>
