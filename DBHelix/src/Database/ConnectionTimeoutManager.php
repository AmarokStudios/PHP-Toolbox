<?php

namespace DBHelix\Database;

class ConnectionTimeoutManager {
    private $timeout;

    public function __construct($timeout = 30) {
        $this->timeout = $timeout;
    }

    public function manageTimeout($connection) {
        $connection->setAttribute(\PDO::ATTR_TIMEOUT, $this->timeout);
    }
}
?>
