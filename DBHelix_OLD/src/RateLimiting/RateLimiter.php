<?php

namespace DBHelix\RateLimiting;

class RateLimiter {
    private $limits;
    private $requests;

    public function __construct($limits) {
        $this->limits = $limits;
        $this->requests = [];
    }

    public function checkRateLimit($key) {
        if (!isset($this->requests[$key])) {
            $this->requests[$key] = [];
        }
        $this->requests[$key][] = time();
        $this->requests[$key] = array_filter($this->requests[$key], function ($timestamp) {
            return $timestamp > time() - 60;
        });
        return count($this->requests[$key]) <= $this->limits;
    }
}
?>
