<?php

namespace DBHelix\Security;

class SqlInjectionPrevention {
    private $database;

    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }

    public function executeQuery($query, $params) {
        $stmt = $this->database->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key + 1, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
