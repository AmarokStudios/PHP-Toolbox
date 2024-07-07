<?php

// Configuration
$config = new Config([
    'driver' => 'mysql',
    'host' => 'localhost',
    'dbname' => 'test_db',
    'username' => 'root',
    'password' => '',
]);

// Container setup
$container = new Container();

$container->set('Config', function() use ($config) {
    return $config;
});

$container->set('Logger', function() {
    return new Logger('database.log');
});

$container->set('Database', function($container) {
    return new Database($container->get('Config'), $container->get('Logger'));
});

$container->set('UserRepository', function($container) {
    return new UserRepository($container->get('Database'));
});

$container->set('UserService', function($container) {
    return new UserService($container->get('UserRepository'));
});

// Usage
$userService = $container->get('UserService');
$users = $userService->getAllUsers();
print_r($users);

?>
