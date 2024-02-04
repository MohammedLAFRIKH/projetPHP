<?php

namespace App;

class Database {
    private static $instance;
    private $conn;

    private function __construct() {
        $host = 'localhost';
        $username = 'root';
        $password = '';
        $database = 'apogee_ens';

        $this->conn = new \mysqli($host, $username, $password, $database);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
