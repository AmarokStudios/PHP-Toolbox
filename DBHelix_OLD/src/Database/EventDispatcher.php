<?php

namespace DBHelix\Database;

class EventDispatcher {
    private $listeners = [];

    public function addListener($event, $listener) {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }
        $this->listeners[$event][] = $listener;
    }

    public function dispatch($event, $data) {
        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $listener) {
                $listener($data);
            }
        }
    }
}
?>
