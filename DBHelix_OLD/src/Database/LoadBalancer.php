<?php

namespace DBHelix\Database;

class LoadBalancer {
    private $databases;
    private $currentIndex = 0;

    public function __construct($databases) {
        $this->databases = $databases;
    }

    public function getDatabase() {
        $database = $this->databases[$this->currentIndex];
        $this->currentIndex = ($this->currentIndex + 1) % count($this->databases);
        return $database;
    }
}
?>
