<?php

namespace DBHelix\Database;

class BackupManager {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function backup($filePath) {
        // Implementation for backing up the database
        $sql = "BACKUP DATABASE TO DISK='$filePath'";
        return $this->database->execute($sql);
    }

    public function restore($filePath) {
        // Implementation for restoring the database
        $sql = "RESTORE DATABASE FROM DISK='$filePath'";
        return $this->database->execute($sql);
    }
}
?>
