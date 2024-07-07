<?php

namespace DBHelix\Database;

class QueryBuilder {
    private $query;
    private $params;

    public function __construct() {
        $this->query = '';
        $this->params = [];
    }

    public function select($columns) {
        $this->query = 'SELECT ' . implode(', ', $columns);
        return $this;
    }

    public function from($table) {
        $this->query .= ' FROM ' . $table;
        return $this;
    }

    public function where($condition, $params) {
        $this->query .= ' WHERE ' . $condition;
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    public function build() {
        return [$this->query, $this->params];
    }
}
?>
