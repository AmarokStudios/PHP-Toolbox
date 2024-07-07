<?php

interface RepositoryInterface {
    public function find($id);
    public function findAll();
    public function save($entity);
    public function delete($entity);
}

?>
