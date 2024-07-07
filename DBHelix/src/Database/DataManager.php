<?php

namespace DBHelix\Database;

class DataManager {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function importFromCSV($tableName, $csvFilePath) {
        // Implementation for importing data from a CSV file
        $sql = "LOAD DATA INFILE '$csvFilePath' INTO TABLE $tableName FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n' IGNORE 1 ROWS";
        return $this->database->execute($sql);
    }

    public function exportToCSV($tableName, $csvFilePath) {
        // Implementation for exporting data to a CSV file
        $sql = "SELECT * FROM $tableName INTO OUTFILE '$csvFilePath' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n'";
        return $this->database->execute($sql);
    }
}
?>
