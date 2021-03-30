<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/includes/DAO.php';

class RequestHandler {
    private $DAO;

    public function __construct() {
        $this->DAO = new DAO();
    }

    //File names must be unique so this is a little helper to make a guid prefix
    private function generateGUID($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function user($data) {
        if($_SERVER['REQUEST_METHOD'] != 'GET' || !isset($_SESSION['id'])) {
            $this->DAO->status(500);
        }

        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL user_requests(:id)');
        $stmt->bindParam(":id", $_SESSION['id']);

        $res = $stmt->execute();
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!$res) {
            $this->DAO->status(500);
        }
        // var_dump($row);
        header('Content-Type: application/json');
        return $rows;
    }

    public function accepted($data) {
        if(
            !isset($_SESSION['user'])
            || !($_SESSION['user']['isCreator'] == 1 || $_SESSION['user']['isAdmin'] == 1)
            || $_SERVER['REQUEST_METHOD'] != 'GET'
            ) {
            $this->DAO->status(500);
        }

        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL accepted_requests(:id)');
        $stmt->bindParam(":id", $_SESSION['id']);

        $res = $stmt->execute();
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!$res) {
            $this->DAO->status(500);
        }
        // var_dump($row);
        header('Content-Type: application/json');
        return $rows;
    }

    // All product does not require anything
    public function all($data = []) {
        if(
            !isset($_SESSION['user'])
            || !($_SESSION['user']['isCreator'] == 1 || $_SESSION['user']['isAdmin'] == 1)
            || $_SERVER['REQUEST_METHOD'] != 'GET'
            ) {
            $this->DAO->status(500);
        }
        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL all_requests()');

        $res = $stmt->execute();
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!$res) {
            $this->DAO->status(500);
        }
        header('Content-Type: application/json');
        return $rows;
    }

    public function canAccess($data) {
        if(!isset($_SESSION['id']) || $_SERVER['REQUEST_METHOD'] != 'GET' || count($data) != 4) {
            $this->DAO->status(500);
        }
        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL can_access_request(:id, :user)');    
        $stmt->bindParam(":id", $data[3]);
        $stmt->bindParam(":user", $_SESSION['id']);

        $res = $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$res) {
            return 'here';
            $this->DAO->status(500);
        }
        // var_dump($row);
        // header('Content-Type: application/json');
        return $row;
    }

    public function default($data) {
        if(!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] != 'GET' || count($data) != 3) {
            $this->DAO->status(500);
        }

        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL get_request(:id)');
        $stmt->bindParam(":id", $data[2]);

        $res = $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$res) {
            $this->DAO->status(500);
        }
        // Query success, nothing retreived
        if(!$row) {
            $this->DAO->status(404);
        }
        // var_dump($row);
        header('Content-Type: application/json');
        return $row;
    }

    // Check for delete method purely for consistency between API behavior and request method
    public function delete($data) {
        if($_SERVER['REQUEST_METHOD'] != "DELETE" || !isset($_SESSION['user']) || count($data) != 4) {
            $this->DAO->status(500);
        }
        // user status
        // Product status. Does the user own this product?
        $connection = $this->DAO->getConnection();
        // Server side verification that a user can delete a request. Technically admins & creators can delete them with this
        // NOTE TO SELF: Should probably have another access method request_owner returns if reequest belongs to session user in the future but this will do for the time being
        $stmt = $connection->prepare('CALL can_access_request(:id, :user)');
        $stmt->bindParam(":id", $data[3]);
        $stmt->bindParam(":user", $_SESSION['user']['id']);

        $res = $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$res) {
            $this->DAO->status(500);
        }
        if(intval($row['access']) == 0) {
            $this->DAO->status(401);
        }
        // All clear
        $stmt = $connection->prepare('CALL delete_request(:id)');
        $stmt->bindParam(":id", $data[3]);

        $res = $stmt->execute();
        
        if(!$res) {
            $this->DAO->status(500);
        }
        // var_dump($row);
        header('Content-Type: application/json');
        return array(0=>"true");
    }

    public function claim($data) {
        if(
        $_SERVER['REQUEST_METHOD'] != "POST"
        || !isset($_SESSION['user'])
        || count($data) != 4
        ) {
            $this->DAO->status(500);
        }
        // user status
        if(!($_SESSION['user']['isCreator'] == 1 || $_SESSION['user']['isAdmin'] == 1))
            $this->DAO->status(401);
        
        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL claim_request(:id, :user)');
        $stmt->bindParam(":id", $data[3]);
        $stmt->bindParam(":user", $_SESSION['id']);

        $res = $stmt->execute();
        
        if(!$res) {
            $this->DAO->status(500);
        }
        // var_dump($row);
        header('Content-Type: application/json');
        return array(0=>"true");
    }

    public function new($data) {

        // General params. Without these API claims it doesn't understand and dies.
        if(
            $_SERVER['REQUEST_METHOD'] != 'POST'
            || !isset($_POST['name'])
            || !isset($_POST['description'])
            || !isset($_SESSION['id'])
            ) {
                $this->DAO->status(500);
        }
        
        // User status. API understands request, but user is not authorized to create requests
        // Creators are not allowed. Admins can do whatever they want because admin accounts are for testing
        if(
            !isset($_SESSION['user'])
            || intval($_SESSION['user']['isCreator']) == 1
            ) {
                $this->DAO->status(401);
        }
        // API understands request, but file is too large
        if(count($_FILES) == 1 && $_FILES['image']['error'] == 1) {
            // Some comment to test pushes after gitignore change
            $this->DAO->status(400);
        }
        // Save image to site
        // Ideally image are actually stored on a third party host to save space, but for now this will do
        // All clear, go ahead with update
        $uploadfile = '';
        // return var_dump($_FILES);
        $image = false;
        if(count($_FILES) == 1 && isset($_FILES['image']) && $_FILES['image']['size'] != 0) {
            $image = true;
            $uploaddir = '/resources/uploads/';
            if(!file_exists($_SERVER['DOCUMENT_ROOT'].$uploaddir))
            mkdir($_SERVER['DOCUMENT_ROOT'].$uploaddir, 0777, true);
            $uploadfile = $uploaddir . $this->generateGUID(5)."_".basename($_FILES['image']['name']);
            
            // Low chance it will happen, but keep checking if the generated filename exists
            // Most of the time this while loop will never run

            while(file_exists($_SERVER['DOCUMENT_ROOT'].$uploadfile))
                $uploadfile = $uploaddir . $this->generateGUID(5)."_".basename($_FILES['image']['name']);

            // Save file
            // return $uploadfile;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$uploadfile)) {
                return var_dump($_FILES);
                $this->DAO->status(500);
            }
            // File is uploaded and we can continue on
        }
        
        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL new_request(:user, :name, :desc, :img)');
        $stmt->bindParam(":name", $_POST['name']);
        $stmt->bindParam(":user", $_SESSION['user']['id']);
        $stmt->bindParam(":desc", $_POST['description']);
        $stmt->bindParam(":img", $uploadfile);

        $res = $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$res) {
            if($image) unlink($_SERVER['DOCUMENT_ROOT'].$uploadfile);
            $this->DAO->status(500);
        }
        
        header('Content-Type: application/json');
        return $row;
    }
}

?>