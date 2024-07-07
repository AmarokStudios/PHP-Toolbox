<?php

namespace DBHelix\Database;

class Migration {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function run($migrations) {
        foreach ($migrations as $migration) {
            $this->database->execute($migration);
        }
    }
}
?>
