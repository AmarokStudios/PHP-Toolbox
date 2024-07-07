<?php

namespace DBHelix\Database;

class AdaptiveQueryOptimizer {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function optimizeQuery($query) {
        // Simple heuristic: use an index if the table has more than 1000 rows
        $table = $this->extractTableName($query);
        $rowCount = $this->database->query("SELECT COUNT(*) as count FROM $table")[0]['count'];
        
        if ($rowCount > 1000) {
            // Add an index hint if there are more than 1000 rows
            $query = str_replace('SELECT', 'SELECT /*+ INDEX() */', $query);
        }

        return $query;
    }

    private function extractTableName($query) {
        // Extract the table name from the query
        preg_match('/FROM\s+(\w+)/i', $query, $matches);
        return $matches[1];
    }
}
?>
