<?php

namespace DBHelix\Utils;

class AdvancedLogger extends Logger {
    public function logQuery($query, $params) {
        $this->log('QUERY: ' . $query . ' PARAMS: ' . json_encode($params));
    }

    public function logError($error) {
        $this->log('ERROR: ' . $error);
    }
}
?>