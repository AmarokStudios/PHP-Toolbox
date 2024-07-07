
# Database Interaction System

A modular database interaction system with features such as secure database operations, logging, and support for dependency injection. The system is designed to be easily extendable and maintainable.

## Features

- **Secure Database Operations**: Perform secure queries, inserts, updates, and deletes.
- **Transaction Management**: Supports transactions and savepoints.
- **Logging**: Logs database queries and errors.
- **Dependency Injection**: Uses a DI container for managing dependencies.
- **Email Notifications**: Sends notifications using SMTP.
- **Role-Based Access Control (RBAC)**: Example implementation for user role management.
- **Pagination**: Fetches records with pagination support.

## Getting Started

### Prerequisites

- PHP 7.4 or higher
- MySQL

### Installation

1. Clone the repository:

```bash
git clone https://github.com/yourusername/database-interaction-system.git
cd database-interaction-system
```

2. Set up the database:

```sql
CREATE DATABASE test_db;
USE test_db;

CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Roles VARCHAR(255) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

3. Configure your environment variables in a `.env` file:

```
DB_HOST=localhost
DB_NAME=test_db
DB_USERNAME=root
DB_PASSWORD=
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USERNAME=your_email@example.com
SMTP_PASSWORD=your_password
```

### Usage

#### Configuration

```php
require 'Config.php';
require 'Container.php';
require 'Database.php';
require 'Logger.php';
require 'UserRepository.php';
require 'UserService.php';
require 'EmailNotifier.php';

// Load environment variables
$dotenv = parse_ini_file('.env');

$config = new Config([
    'driver' => 'mysql',
    'host' => $dotenv['DB_HOST'],
    'dbname' => $dotenv['DB_NAME'],
    'username' => $dotenv['DB_USERNAME'],
    'password' => $dotenv['DB_PASSWORD'],
    'email' => [
        'smtp_host' => $dotenv['SMTP_HOST'],
        'smtp_port' => $dotenv['SMTP_PORT'],
        'username' => $dotenv['SMTP_USERNAME'],
        'password' => $dotenv['SMTP_PASSWORD'],
    ],
]);

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

$container->set('EmailNotifier', function($container) {
    return new EmailNotifier($container->get('Config')->get('email'));
});

$container->set('UserService', function($container) {
    return new UserService($container->get('UserRepository'), $container->get('EmailNotifier'));
});

$userService = $container->get('UserService');
```

#### Basic Database Operations

##### Inserting Data

```php
$newUser = [
    'Name' => 'Alice',
    'Email' => 'alice@example.com',
    'Password' => password_hash('secretpassword', PASSWORD_BCRYPT),
    'Roles' => 'user',
];
$userService->saveUser($newUser);
```

##### Selecting Data

```php
$users = $userService->getAllUsers();
print_r($users);
```

##### Updating Data

```php
$userToUpdate = [
    'id' => 1,
    'Name' => 'Alice Updated',
    'Email' => 'alice.updated@example.com',
];
$userService->saveUser($userToUpdate);
```

##### Deleting Data

```php
$userToDelete = ['id' => 1];
$userService->deleteUser($userToDelete);
```

#### Transaction Management

```php
$database = $container->get('Database');

// Start a transaction
$database->beginTransaction();

try {
    // Perform some operations
    $newUser = [
        'Name' => 'Bob',
        'Email' => 'bob@example.com',
        'Password' => password_hash('secretpassword', PASSWORD_BCRYPT),
        'Roles' => 'user',
    ];
    $userService->saveUser($newUser);

    // Create a savepoint
    $database->savepoint('savepoint1');

    // Perform more operations
    $anotherUser = [
        'Name' => 'Charlie',
        'Email' => 'charlie@example.com',
        'Password' => password_hash('secretpassword', PASSWORD_BCRYPT),
        'Roles' => 'user',
    ];
    $userService->saveUser($anotherUser);

    // Rollback to the savepoint
    $database->rollbackToSavepoint('savepoint1');

    // Commit the transaction
    $database->commit();
    echo "Transaction committed successfully!";
} catch (Exception $e) {
    $database->rollBack();
    echo "Transaction failed: " . $e->getMessage();
}
```

#### Email Notifications

The `EmailNotifier` class is used to send notifications. Here's an example of sending an email:

```php
$emailNotifier = $container->get('EmailNotifier');

$subject = "Test Email";
$message = "This is a test email.";
$emailNotifier->send('recipient@example.com', $subject, $message);
```

### Directory Structure

```
database-interaction-system/
├── Config.php
├── Container.php
├── Database.php
├── Logger.php
├── UserRepository.php
├── UserService.php
├── EmailNotifier.php
├── .env
└── README.md
```

### License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

### Acknowledgments

- [PHP](https://www.php.net/)
- [PHPMailer](https://github.com/PHPMailer/PHPMailer) (optional for more robust email functionality)
