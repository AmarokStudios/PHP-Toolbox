<?php

namespace DBHelix\Database;

class ConnectionPoolingManager {
    private $pool = [];
    private $maxConnections;
    private $databaseConfig;

    public function __construct($databaseConfig, $maxConnections = 10) {
        $this->databaseConfig = $databaseConfig;
        $this->maxConnections = $maxConnections;
    }

    public function getConnection() {
        if (count($this->pool) > 0) {
            return array_pop($this->pool);
        } else {
            return new Database($this->databaseConfig);
        }
    }

    public function releaseConnection($connection) {
        if (count($this->pool) < $this->maxConnections) {
            $this->pool[] = $connection;
        }
    }
}
?>
