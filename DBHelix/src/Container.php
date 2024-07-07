<?php

class Container {
    private $bindings = [];

    public function set($name, $resolver) {
        $this->bindings[$name] = $resolver;
    }

    public function get($name) {
        if (isset($this->bindings[$name])) {
            return call_user_func($this->bindings[$name]);
        }
        throw new Exception("Service not found: " . $name);
    }
}

?>
