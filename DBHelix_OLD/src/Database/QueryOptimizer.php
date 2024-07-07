<?php

namespace DBHelix\Database;

class QueryOptimizer {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function analyzeQuery($query) {
        $sql = "EXPLAIN " . $query;
        return $this->database->query($sql);
    }

    public function optimizeIndexes($table) {
        $sql = "SHOW INDEX FROM " . $table;
        $indexes = $this->database->query($sql);

        // Basic optimization example: check for duplicate indexes
        $indexNames = array_column($indexes, 'Key_name');
        $duplicateIndexes = array_diff_assoc($indexNames, array_unique($indexNames));

        return ['indexes' => $indexes, 'duplicates' => $duplicateIndexes];
    }
}
?>
