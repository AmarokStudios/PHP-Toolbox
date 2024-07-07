<?php

namespace DBHelix\Cache;

class CacheManager {
    private $cache = [];

    public function set($key, $value, $ttl = 3600) {
        $this->cache[$key] = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
    }

    public function get($key) {
        if (isset($this->cache[$key]) && $this->cache[$key]['expires'] > time()) {
            return $this->cache[$key]['value'];
        }
        return null;
    }

    public function delete($key) {
        unset($this->cache[$key]);
    }
}
?>
