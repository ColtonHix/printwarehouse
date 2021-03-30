<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/includes/DAO.php';

class UserHandler {
    private $DAO;

    public function __construct() {
        $this->DAO = new DAO();
    }

    public function new($data) {
        // Get for testing, swap to post for prod
        if(
            $_SERVER['REQUEST_METHOD'] != 'POST'
            || !isset($_POST['username'])
            || !preg_match("/^[a-zA-Z0-9\\-\\_]{3,25}$/", $_POST['username']) // Sever side username verification
            || !isset($_POST['password'])
            || (null == filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
            ) {
                $this->DAO->status(500);
        }
        $enc = array(
            "username" => $_POST['username'],
            "password" => password_hash($_POST["password"], PASSWORD_DEFAULT),
            "email" => $_POST['email']
        );
        
        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL user_exists(:email, :username)');
        $stmt->bindParam(":email", $enc['email']);
        $stmt->bindParam(":username", $enc['username']);

        $res = $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) {
            $this->DAO->status(500);
        }
        if($row['Email'] || $row['Username']) {
            header('Content-Type: application/json');
            header($_SERVER['SERVER_PROTOCOL'] . " 401 Unauthorized", true, 401);
            return $row;
        }

        $stmt = $connection->prepare('CALL new_user(:email, :pass, :username)');
        $stmt->bindParam(":email", $enc['email']);
        $stmt->bindParam(":pass", $enc['password']);
        $stmt->bindParam(":username", $enc['username']);

        $res = $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) {
            $this->DAO->status(500);
        }
        $_SESSION['id'] = $row['id'];
        $data[2] = "data";
        $data[3] = $_SESSION['id'];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SESSION['user'] = $this->data($data);
        unset($_SESSION['user']['password']);
        $row = $_SESSION['user'];
        // User doesn't need this
        unset($row['password']);

        header('Content-Type: application/json');
        return $row;
    }

    // Get a users data if they are the logged in user
    public function data($data) {

        if($_SERVER['REQUEST_METHOD'] != 'GET' || count($data) != 4) {
            $this->DAO->status(500);
        }

        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL get_user(:id)');
        $stmt->bindParam(":id", $data[3]);

        $res = $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$res) {
            $this->DAO->status(500);
        }
        // var_dump($row);
        header('Content-Type: application/json');
        return $row;
    }

    public function login($data) {
        // Get for testing, swap to post for prod
        if(
        $_SERVER['REQUEST_METHOD'] != 'POST'
        || !isset($_POST['username'])
        || !isset($_POST['password'])
        ) {
            $this->DAO->status(500);
        }
        $enc = array(
            "username" => $_POST['username'],
            "password" => password_hash($_POST["password"], PASSWORD_DEFAULT)
        );
        
        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL user_hash(:username)');
        $stmt->bindParam(":username", $enc['username']);

        $res = $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Query died
        if(!$res) {
            $this->DAO->status(500);
        }
        $row['verify'] = password_verify($_POST['password'], $row["password"]);
        // Query successfully ran but returned nothing
        if(!$row['verify']) {
            $this->DAO->status(401);
        }
        if($row['verify']) {
            $_SESSION['id'] = $row['id'];
            $data[2] = "data";
            $data[3] = $_SESSION['id'];
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SESSION['user'] = $this->data($data);
            $_SESSION['user']['isCreator'] = intval($_SESSION['user']['isCreator']);
            $_SESSION['user']['isAdmin'] = intval($_SESSION['user']['isAdmin']);
            unset($_SESSION['user']['password']);
            $row = $_SESSION['user'];
            // User doesn't need this
            unset($row['password']);
        }

        header('Content-Type: application/json');
        return $row;
    }
}

?>