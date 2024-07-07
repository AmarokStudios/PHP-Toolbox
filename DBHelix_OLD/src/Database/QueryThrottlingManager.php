<?php

namespace DBHelix\Database;

class QueryThrottlingManager {
    private $maxConcurrentQueries;
    private $currentQueries = 0;

    public function __construct($maxConcurrentQueries = 5) {
        $this->maxConcurrentQueries = $maxConcurrentQueries;
    }

    public function startQuery() {
        while ($this->currentQueries >= $this->maxConcurrentQueries) {
            usleep(100000); // Wait for 100ms before checking again
        }
        $this->currentQueries++;
    }

    public function endQuery() {
        $this->currentQueries--;
    }
}
?>
