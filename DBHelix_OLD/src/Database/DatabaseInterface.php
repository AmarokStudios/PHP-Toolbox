<?php

interface DatabaseInterface {
    public function query($SQL, $Params = []);
    public function select($Table, $Columns = '*', $Where = '', $Params = [], $Cache = false, $CacheDuration = 60);
    public function insert($Table, $Data);
    public function update($Table, $Data, $Where, $Params = []);
    public function delete($Table, $Where, $Params = []);
    public function countRows($Table, $Where = '', $Params = []);
    public function beginTransaction();
    public function commit();
    public function rollBack();
    public function savepoint($SavepointName);
    public function rollbackToSavepoint($SavepointName);
    public function getConnection();
}

?>
