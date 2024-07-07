<?php

class UserService {
    private $UserRepository;
    private $EmailNotifier;

    public function __construct(UserRepository $UserRepository, EmailNotifier $EmailNotifier) {
        $this->UserRepository = $UserRepository;
        $this->EmailNotifier = $EmailNotifier;
    }

    public function getUser($id) {
        return $this->UserRepository->find($id);
    }

    public function getAllUsers($limit = null, $offset = null) {
        return $this->UserRepository->findAll($limit, $offset);
    }

    public function saveUser($user) {
        $this->UserRepository->save($user);
    }

    public function deleteUser($user) {
        $this->UserRepository->delete($user);
    }

    public function authenticate($email, $password) {
        return $this->UserRepository->authenticate($email, $password);
    }

    public function hasRole($user, $role) {
        // Basic role-based access control
        return in_array($role, explode(',', $user['Roles']));
    }

    public function registerUser($user) {
        if (filter_var($user['Email'], FILTER_VALIDATE_EMAIL) === false) {
            throw new Exception("Invalid email address.");
        }
        if (strlen($user['Password']) < 6) {
            throw new Exception("Password must be at least 6 characters long.");
        }
        
        // Check if the email is already taken
        if ($this->UserRepository->findByEmail($user['Email'])) {
            throw new Exception("Email is already registered.");
        }

        // Hash the password
        $user['Password'] = password_hash($user['Password'], PASSWORD_BCRYPT);

        // Save the user
        $this->saveUser($user);

        // Send a welcome email
        $subject = "Welcome to Our Service";
        $message = "Hello " . $user['Name'] . ",\n\nThank you for registering with our service!";
        $this->EmailNotifier->send($user['Email'], $subject, $message);
    }
}



?>
