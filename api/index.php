<?php

require_once 'Controller.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri,"/");
$uri = preg_split('/(?<!^)\/(?!$)/', $uri );

$gates = [];
$gates[] = "user";
$gates[] = "product";
$gates[] = "request";

$gate = $uri[1];
// If the specified gateway is not supported kill the page
if(!in_array($gate,$gates)) {
    header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error", true, 500);
    die();
}

// Current setup does not really allow for a default method (like hitting /product/2 ) so a work around is to check the length
$method = $uri[2];

$controller = new Controller($gate);
$outputs = $controller->processRequest($method, $uri);
// Santize output
// Good thing about having an API format is all outputs reroute to here (except for the one or two pages that dont...)
$sanitized = array();
if(is_array($outputs)) {
    // Psych just kidding ALL calls that aren't binary are arrays
    // Need to santize inner arrays at most of once; there are never nested rows returned
    foreach($outputs as $i=>$row) {
        if(is_array($row)) {
            $sRow = array();
            foreach($row as $key=>$value)
                $sRow[$key] = htmlspecialchars($value);
        } else {
            $sRow = htmlspecialchars($row);
        }
        $sanitized[$i] = $sRow;
    }
} else {
    foreach($outputs as $key=>$value) {
        $sanitized[$key] = htmlspecialchars($value);
    }
}
echo json_encode($sanitized);

?>