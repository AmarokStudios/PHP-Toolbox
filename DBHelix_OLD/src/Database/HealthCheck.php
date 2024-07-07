<?php

namespace DBHelix\Database;

class HealthCheck {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function checkHealth() {
        try {
            $this->database->query('SELECT 1');
            return ['status' => 'healthy'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }
    }
}
?>
