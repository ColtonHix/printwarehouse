<?php

class DAO {
    private $dsn;
    private $user;
    private $password;
    
    // Helper function to reduce duplicated code
    public function status($code) {
        switch ($code) {
            case 500:
                header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error", true, 500);
                die();
            case 401:
                header($_SERVER['SERVER_PROTOCOL'] . " 401 Unauthorized", true, 401);
                die();
            case 404:
                header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
                die();
            case 400:
                header($_SERVER['SERVER_PROTOCOL'] . " 400 Bad Request", true, 400);
                die();
        }
    }

    public function __construct () {
        if(file_exists($_SERVER['DOCUMENT_ROOT']."/config.json")) {
        $file = file_get_contents($_SERVER['DOCUMENT_ROOT']."/config.json");
        $vals = json_decode($file);
        $this->dsn = $vals->dsn;
        $this->user = $vals->user;
        $this->password = $vals->password;
    } else {
        $this->dsn = 'mysql:dbname=heroku_a73321e07c9d6e8;host=us-cdbr-east-03.cleardb.com';
        $this->user = "bca9c02fbd73e6";
        $this->password = "55a2304f";
    }
    }

    public function getConnection () {
        $connection = null;
    try {
        $connection = new PDO($this->dsn, $this->user, $this->password);
    } catch (PDOException $e) {
        $error = 'Connection failed: ' . $e->getMessage();
        echo $error;
    }
    return $connection;
    }
}

// Safe to assume anything that includes the DAO (handlers) will modify session
if(session_status() != 2) session_start();
?>