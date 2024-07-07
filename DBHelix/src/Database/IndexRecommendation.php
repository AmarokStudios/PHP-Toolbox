<?php

namespace DBHelix\Database;

class IndexRecommendation {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function recommendIndexes($table) {
        // This is a basic example. In reality, you would use more sophisticated analysis.
        $sql = "SHOW COLUMNS FROM " . $table;
        $columns = $this->database->query($sql);

        // Recommend indexes on all columns that are not primary keys or already indexed
        $indexRecommendations = [];
        foreach ($columns as $column) {
            if ($column['Key'] == '') {
                $indexRecommendations[] = $column['Field'];
            }
        }

        return $indexRecommendations;
    }
}
?>
