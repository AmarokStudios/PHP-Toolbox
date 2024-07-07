<?php

namespace DBHelix\Database;

class MemoryUsageMonitor {
    public function logMemoryUsage($query) {
        $memoryUsage = memory_get_usage();
        $sql = "INSERT INTO memory_usage_log (query, memory_usage, timestamp) VALUES (?, ?, NOW())";
        $this->database->execute($sql, [$query, $memoryUsage]);
    }

    public function getMemoryUsageStats() {
        $sql = "SELECT * FROM memory_usage_log ORDER BY timestamp DESC";
        return $this->database->query($sql);
    }
}
?>
