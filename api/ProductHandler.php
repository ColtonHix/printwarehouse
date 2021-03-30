<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/includes/DAO.php';

class ProductHandler {
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

    private function price($price) {
        return $price >= 0.01 && $price <= 1500.00;
    }

    public function user($data) {
        if($_SERVER['REQUEST_METHOD'] != 'GET' || count($data) != 4) {
            $this->DAO->status(500);
        }

        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL get_products(:id)');
        $stmt->bindParam(":id", $data[3]);

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
    public function all($data) {
        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL all_products()');

        $res = $stmt->execute();
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!$res) {
            $this->DAO->status(500);
        }
        header('Content-Type: application/json');
        return $rows;
    }

    public function default($data) {
        if($_SERVER['REQUEST_METHOD'] != 'GET' || count($data) != 3) {
            $this->DAO->status(500);
        }

        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL get_product(:id)');
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
            return "asdf";
            $this->DAO->status(500);
        }
        // user status
        // Product status. Does the user own this product?
        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL can_update(:id, :user)');
        $stmt->bindParam(":id", $data[3]);
        $stmt->bindParam(":user", $_SESSION['user']['id']);

        $res = $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$res) {
            $this->DAO->status(500);
        }
        if(intval($row['valid']) == 0) {
            $this->DAO->status(401);
        }
        $img = $row['Image'];
        // All clear
        $stmt = $connection->prepare('CALL delete_product(:id)');
        $stmt->bindParam(":id", $data[3]);

        $res = $stmt->execute();
        
        if(!$res) {
            $this->DAO->status(500);
        }
        // Product successfully deleted, finish cleanup by deleting the image
        unlink($_SERVER['DOCUMENT_ROOT'].$img);
        // var_dump($row);
        header('Content-Type: application/json');
        return array(0=>"true");
    }

    public function update($data) {
        // General params. Without these API claims it doesn't understand and dies.
        if(
            $_SERVER['REQUEST_METHOD'] != 'POST'
            || !isset($_POST['name'])
            || !isset($_POST['price'])
            || !$this->price(floatval($_POST['price'])) // Sever side verification of price, the only thing that needs a pattern
            || !isset($_POST['description'])
            || count($data) != 4
            ) {
                $this->DAO->status(500);
        }
        // API understands request, but file is too large
        if(count($_FILES) == 1 && $_FILES['image']['error'] == 1) {
            // Some comment to test pushes after gitignore change
            $this->DAO->status(400);
        }
        // User status. API understands request, but user is not authorized to create/update products
        if(
            !isset($_SESSION['user'])
            || !(intval($_SESSION['user']['isCreator']) != 1
            || intval($_SESSION['user']['isAdmin']) != 1)
            ) {
                // return (intval($_SESSION['user']['isCreator']) != 1
                // || intval($_SESSION['user']['isAdmin']) != 1);
                $this->DAO->status(401);
        }
        // Product status. Does the user own this product?
        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL can_update(:id, :user)');
        $stmt->bindParam(":id", $data[3]);
        $stmt->bindParam(":user", $_SESSION['user']['id']);

        $res = $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$res) {
            $this->DAO->status(500);
        }
        // User does not have permission
        if(intval($row['valid']) == 0) {
            $this->DAO->status(401);
        }
        // All clear, go ahead with update
        $uploadfile = '';
        $newFile = false;
        if(count($_FILES) == 1 && isset($_FILES['image']) && $_FILES['image']['size'] != 0) {
            $newFile = true;
            $uploaddir = '/resources/uploads/';
            $uploadfile = $uploaddir . $this->generateGUID(5)."_".basename($_FILES['image']['name']);
            
            // Low chance it will happen, but keep checking if the generated filename exists
            // Most of the time this while loop will never run

            while(file_exists($_SERVER['DOCUMENT_ROOT'].$uploadfile))
                $uploadfile = $uploaddir . $this->generateGUID(5)."_".basename($_FILES['image']['name']);

            // Save file
            // return $uploadfile;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$uploadfile)) {
                $this->DAO->status(500);
            }
            // File is uploaded and we can continue on
            // Delete the old image
            unlink($_SERVER['DOCUMENT_ROOT'].$row['Image']);
        }
        
        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL update_product(:name, :desc, :img, :price, :id)');
        $stmt->bindParam(":name", $_POST['name']);
        $stmt->bindParam(":desc", $_POST['description']);
        $stmt->bindParam(":img", $uploadfile);
        $stmt->bindParam(":price", $_POST["price"]);
        $stmt->bindParam(":id", $data[3]);


        $res = $stmt->execute();

        if(!$res) {
            if($newFile)
                unlink($_SERVER['DOCUMENT_ROOT'].$uploadfile);
            $this->DAO->status(500);
        }
        
        return array(0=>"true");
    }

    public function new($data) {
        // General params. Without these API claims it doesn't understand and dies.
        if(
            $_SERVER['REQUEST_METHOD'] != 'POST'
            || !isset($_POST['name'])
            || !isset($_POST['price'])
            || !$this->price(floatval($_POST['price'])) // Sever side verification of price, the only thing that needs a pattern
            || !isset($_POST['description'])
            ) {
                $this->DAO->status(500);
        }

        // API understands request, but file is too large
        if(empty($_FILES) || count($_FILES) != 1 || intval($_FILES['image']['error']) == 1) {
            $this->DAO->status(400);
        }
        
        // User status. API understands request, but user is not authorized to create products
        // return var_dump($_SESSION['user']);
        if(
            !isset($_SESSION['user'])
            || !(intval($_SESSION['user']['isCreator']) != 1
            || intval($_SESSION['user']['isAdmin']) != 1)
            ) {
                // return (intval($_SESSION['user']['isCreator']) != 1
                // || intval($_SESSION['user']['isAdmin']) != 1);
                $this->DAO->status(401);
        }
        // Save image to site
        // Ideally image are actually stored on a third party host to save space, but for now this will do
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
        // Document location needs to be stored as relative in DB, but must move absolutely in php
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$uploadfile)) {
            $this->DAO->status(500);
        }
        // File is uploaded and we can continue on
        
        $connection = $this->DAO->getConnection();
        $stmt = $connection->prepare('CALL new_product(:name, :user, :desc, :img, :price)');
        $stmt->bindParam(":name", $_POST['name']);
        $stmt->bindParam(":user", $_SESSION['user']['id']);
        $stmt->bindParam(":desc", $_POST['description']);
        $stmt->bindParam(":img", $uploadfile);
        $stmt->bindParam(":price", $_POST["price"]);

        $res = $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$res) {
            // If the query went wrong, delete the image
            unlink($_SERVER['DOCUMENT_ROOT'].$uploadfile);
            $this->DAO->status(500);
        }
        
        header('Content-Type: application/json');
        return $row;
    }
}

?>