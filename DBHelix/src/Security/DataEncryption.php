<?php

namespace DBHelix\Security;

class DataEncryption {
    private $key;

    public function __construct($key) {
        $this->key = $key;
    }

    public function encrypt($data) {
        return openssl_encrypt($data, 'aes-256-cbc', $this->key, 0, str_repeat('0', 16));
    }

    public function decrypt($data) {
        return openssl_decrypt($data, 'aes-256-cbc', $this->key, 0, str_repeat('0', 16));
    }
}
?>
