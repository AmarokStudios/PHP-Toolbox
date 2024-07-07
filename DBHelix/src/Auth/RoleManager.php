<?php

namespace DBHelix\Auth;

class RoleManager {
    private $roles = [];

    public function addRole($roleName, $permissions) {
        $this->roles[$roleName] = $permissions;
    }

    public function getPermissions($roleName) {
        return $this->roles[$roleName] ?? [];
    }

    public function hasPermission($roleName, $permission) {
        return in_array($permission, $this->roles[$roleName] ?? []);
    }
}
?>
