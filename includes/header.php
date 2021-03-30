<?php
// Set defaults so header doesn't break if not set
$active = isset($active) ? $active : -1;
$title = isset($title) ? $title :  "Print Warehouse";
$styles = isset($styles) ? $styles : [];
$requireLogin = isset($requireLogin) ? $requireLogin : false;

// Does the page require an ID afix: /product/{id}
$requireId = isset($requireId) ? $requireId : false;

// start session on all pages
if(session_status() != 2) session_start();
if($requireLogin && !isset($_SESSION['id'])) {
    header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
    header("Location: /notfound");
    exit;
}
// If this page (currently one one page) requires user to be originator or creator
// Check it before and if invalid reroute to 401 unathorized
// Any pages that use this contain the request id, but not the id of the requestor
// This is one of the few cases where we need to make a data call without the API
if($requireId) {
    $id = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $id = rtrim($id,"/");
    $id = preg_split('/(?<!^)\/(?!$)/', $id)[1] ?? false;
    if(!$id) {
        header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
        header("Location: /notfound");
        exit;
    }
}
if(isset($requireOriginCreator) && $requireOriginCreator) {
    include $_SERVER['DOCUMENT_ROOT'].'/api/RequestHandler.php';
    $handler = new RequestHandler();
    $id = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $id = rtrim($id,"/");
    $id = preg_split('/(?<!^)\/(?!$)/', $id)[1];
    
    // Modify uri array to match what the api expects
    // Kind a wonky way of using an API but if the boat floats
    $uri = $id = preg_split('/(?<!^)\//', "api/request/canAccess/".$id);
    $res = intval(($handler->canAccess($uri))['access']);
    if($res != 1) {
        header($_SERVER['SERVER_PROTOCOL'] . " 401 Unauthorized", true, 401);
        die();
    }
}
?>

<!--
    Head contains any global scripts, stylesheets, other important information so I want it in a php include
    Page specific styles and values can easily be shoved in with predefined variables
-->

<head>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <!-- jQuery Plugins -->

    <title><?php echo $title?></title>
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="icon" type="image/png" href="/resources/benchy.png">
    <?php
        if(count($styles) > 0) {
            foreach($styles as $style) {
                echo "<link rel=\"stylesheet\" href=\"/styles/${style}.css\"/>";
            }
        }
    ?>
</head>

<body>
    <div id="global-alert" class="alert alert-dismissible fade" role="alert">
        <p class="mb-0" id="alert-body"></p>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="/">
            <img src="/resources/benchy.png" width="50" height="50" class="d-inline-block" alt="">
            Print Warehouse
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li <?php echo 'class="nav-item '.($active == 0 ? 'active' : '').'"' ?>>
                    <a class="nav-link" href="/about">About</a>
                </li>
                <li <?php echo 'class="nav-item '.($active == 1 ? 'active' : '').'"' ?>>
                    <a class="nav-link" href="/sitemap">Sitemap</a>
                </li>
                <li <?php echo 'class="nav-item '.($active == 2 ? 'active' : '').'"' ?>>
                    <a class="nav-link" href="/legal">Legal</a>
                </li>
                <li <?php echo 'class="nav-item '.($active == 4 ? 'active' : '').'"' ?>>
                    <?php
            if(isset($_SESSION['user']) && ($_SESSION['user']['isCreator'] || $_SESSION['user']['isAdmin'])) {
                echo '<a class="nav-link" href="/dashboard">Dashboard</a>';
            }
            ?>
                </li>
                <?php
                if(isset($_SESSION['user']))
                    echo '<li class="nav-item '.($active == 5 ? 'active' : '').'">
                    <a class="nav-link" href="/requests">Requests</a>
                    </li>';
                ?>
                
                <li class="nav-item">
                    <?php
            if(isset($_SESSION['user'])) {
                echo '<a class="nav-link" href="/logout">Logout</a>';
            } else {
                echo '<a href="#" class="nav-link" data-toggle="modal" data-target="#loginModal">Login</a>';
            }
            ?>
                </li>
            </ul>
            <form class="form-inline">
                <div class="input-group mb-3">
                    <input type="search" class="form-control" placeholder="Search" aria-label="Search" id="search">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </nav>
    <!-- <div class="header row button-list">
    <div class="logo col-md"><a href="/"><img src="../resources/benchy.png"/>Print Warehouse</a></div>
    <div class="col-md"><a <?php echo ($active == 0 ? 'class="active"' : '') ?> href="/about.php">About</a></div>
    <div class="col-md"><a <?php echo ($active == 1 ? 'class="active"' : '') ?> href="/sitemap.php">Sitemap</a></div>
    <div class="col-md"><a <?php echo ($active == 2 ? 'class="active"' : '') ?> href="/legal.php">Legal Information</a></div>
    <?php
        if(isset($_SESSION['id'])) {
            echo '<div class="col-md"><a href="/logout">Logout</a></div>';
        } else {
            echo '<div class="col-md"><button type="button" class="header-button" data-toggle="modal" data-target="#loginModal">Login</button></div>';
        }
    ?>
    <div class ="search">
        <form>
            <input type="text" id="search" placeholder="Search...">
        </form>
    </div>
</div> -->