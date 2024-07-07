<?php

namespace DBHelix\Database;

class ConnectionPool {
    private $pool;
    private $maxConnections;
    private $databaseConfig;

    public function __construct($databaseConfig, $maxConnections = 10) {
        $this->pool = [];
        $this->maxConnections = $maxConnections;
        $this->databaseConfig = $databaseConfig;
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
