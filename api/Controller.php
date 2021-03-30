<?php

require_once 'UserHandler.php';
require_once 'ProductHandler.php';
require_once 'RequestHandler.php';

class Controller {

    private $handler;

    public function __construct($type) {
        // I don't doubt there is a better way to do this, even generic functions like in processRequest
        // would work but would still require a separate method for each handler, so this will work for now
        switch($type) {
            case "user":
                $this->handler = new UserHandler();
                break;
            case "product":
                $this->handler = new ProductHandler();
                break;
            case "request":
                $this->handler = new RequestHandler();
                break;
        }
    }

    /**
     * $method - The root method directly following the gateway
     * $data - The sent data
     */
    public function processRequest($method, $data) {
        // PHP Supports variable functions
        // As long as all function names match the gateway methods i can just do this
        $request = array($this->handler, $method);
        if(is_callable($request))
            return $this->handler->$method($data);
        else {
            $request = array($this->handler, "default");
            if(is_callable($request))
                return $this->handler->default($data);
            else {
                header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error", true, 500);
                die();
            }
        }
    }

}

?>