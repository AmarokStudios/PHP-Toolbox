<?php

require 'Config.php';
require 'Container.php';
require 'Database.php';
require 'Logger.php';

// Load environment
$env = $argv[1] ?? 'dev'; // Default to 'dev' if no argument is provided
if (!in_array($env, ['dev', 'test'])) {
    die("Invalid environment specified. Use 'dev' or 'test'.\n");
}

// Set up configuration for production and target environment
$prodConfig = Config::fromEnv('prod');
$targetConfig = Config::fromEnv($env);

// Set up production database
$prodContainer = new Container();
$prodContainer->set('Config', function() use ($prodConfig) {
    return $prodConfig;
});
$prodContainer->set('Logger', function() {
    return new Logger('database.log');
});
$prodContainer->set('Database', function($prodContainer) {
    return new Database($prodContainer->get('Config'), $prodContainer->get('Logger'));
});
$prodDatabase = $prodContainer->get('Database');

// Backup production database
$backupFilePath = __DIR__ . '/backup.sql';
$prodDatabase->backupDatabase($backupFilePath);

// Set up target database
$targetContainer = new Container();
$targetContainer->set('Config', function() use ($targetConfig) {
    return $targetConfig;
});
$targetContainer->set('Logger', function() {
    return new Logger('database.log');
});
$targetContainer->set('Database', function($targetContainer) {
    return new Database($targetContainer->get('Config'), $targetContainer->get('Logger'));
});
$targetDatabase = $targetContainer->get('Database');

// Restore backup to target database
$targetDatabase->restoreDatabase($backupFilePath);

echo "Refreshed {$env} environment from production.\n";
?>
