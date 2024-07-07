<?php

namespace DBHelix\MultiTenancy;

class TenantManager {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function createTenant($tenantId) {
        $sql = "CREATE SCHEMA tenant_$tenantId";
        return $this->database->execute($sql);
    }

    public function dropTenant($tenantId) {
        $sql = "DROP SCHEMA tenant_$tenantId";
        return $this->database->execute($sql);
    }

    public function switchTenant($tenantId) {
        $sql = "USE tenant_$tenantId";
        return $this->database->execute($sql);
    }
}
?>
