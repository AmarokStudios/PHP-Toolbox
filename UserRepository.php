<?php

class UserRepository implements RepositoryInterface {
    private $Database;

    public function __construct(DatabaseInterface $Database) {
        $this->Database = $Database;
    }

    public function find($id) {
        return $this->Database->select('Users', '*', 'id = :id', ['id' => $id]);
    }

    public function findAll($limit = null, $offset = null) {
        $sql = 'SELECT * FROM Users';
        if ($limit !== null && $offset !== null) {
            $sql .= ' LIMIT :limit OFFSET :offset';
            return $this->Database->query($sql, ['limit' => $limit, 'offset' => $offset])->fetchAll();
        }
        return $this->Database->query($sql)->fetchAll();
    }

    public function findByEmail($email) {
        return $this->Database->select('Users', '*', 'Email = :email', ['email' => $email]);
    }

    public function save($user) {
        if (isset($user['id'])) {
            $this->Database->update('Users', $user, 'id = :id', ['id' => $user['id']]);
        } else {
            $user['Password'] = password_hash($user['Password'], PASSWORD_BCRYPT);
            $this->Database->insert('Users', $user);
        }
    }

    public function delete($user) {
        $this->Database->delete('Users', 'id = :id', ['id' => $user['id']]);
    }

    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user[0]['Password'])) {
            return $user[0];
        }
        return false;
    }
}



?>
