<?php

namespace DBHelix\Sharding;

class ShardManager {
    private $shards;

    public function __construct($shards) {
        $this->shards = $shards;
    }

    public function getShard($key) {
        $shardIndex = crc32($key) % count($this->shards);
        return $this->shards[$shardIndex];
    }
}
?>
