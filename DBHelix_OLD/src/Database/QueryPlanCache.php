<?php

namespace DBHelix\Database;

class QueryPlanCache {
    private $cache = [];

    public function cachePlan($query, $plan) {
        $key = md5($query);
        $this->cache[$key] = $plan;
    }

    public function getCachedPlan($query) {
        $key = md5($query);
        return $this->cache[$key] ?? null;
    }
}
?>
