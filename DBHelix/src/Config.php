<?php

class Config {
    private $settings = [];

    public function __construct(array $settings) {
        $this->settings = $settings;
    }

    public function get($key) {
        return $this->settings[$key] ?? null;
    }

    public static function fromEnv($env) {
        $dotenv = parse_ini_file('.env');
        $prefix = strtoupper($env) . '_';

        return new self([
            'driver' => $dotenv['DB_DRIVER'],
            'host' => $dotenv[$prefix . 'DB_HOST'],
            'dbname' => $dotenv[$prefix . 'DB_NAME'],
            'username' => $dotenv[$prefix . 'DB_USERNAME'],
            'password' => $dotenv[$prefix . 'DB_PASSWORD'],
            'email' => [
                'smtp_host' => $dotenv['SMTP_HOST'],
                'smtp_port' => $dotenv['SMTP_PORT'],
                'username' => $dotenv['SMTP_USERNAME'],
                'password' => $dotenv['SMTP_PASSWORD'],
            ],
        ]);
    }
}
?>
