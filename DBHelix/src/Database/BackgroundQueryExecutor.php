<?php

namespace DBHelix\Database;

class BackgroundQueryExecutor {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function executeQuery($query, $params = []) {
        // Run the query in a background process
        $pid = pcntl_fork();
        if ($pid == -1) {
            // Fork failed
            throw new \Exception('Could not fork process');
        } elseif ($pid) {
            // Parent process
            return;
        } else {
            // Child process
            $this->database->execute($query, $params);
            exit(0);
        }
    }
}
?>
