<?php

namespace DBHelix\Database;

class CachingManager {
    private $cache;

    public function __construct() {
        $this->cache = [];
    }

    public function cacheQuery($query, $result, $ttl = 3600) {
        $key = md5($query);
        $this->cache[$key] = [
            'result' => $result,
            'expires' => time() + $ttl
        ];
    }

    public function getCachedQuery($query) {
        $key = md5($query);
        if (isset($this->cache[$key]) && $this->cache[$key]['expires'] > time()) {
            return $this->cache[$key]['result'];
        }
        return null;
    }
}
?>
