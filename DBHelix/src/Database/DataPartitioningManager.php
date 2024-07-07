<?php

namespace DBHelix\Database;

class DataPartitioningManager {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function createPartition($table, $partitionColumn) {
        $sql = "ALTER TABLE $table PARTITION BY HASH($partitionColumn) PARTITIONS 4";
        return $this->database->execute($sql);
    }

    public function dropPartition($table) {
        $sql = "ALTER TABLE $table REMOVE PARTITIONING";
        return $this->database->execute($sql);
    }
}
?>
