<html>
<?php 
$title = "Print Warehouse - Print Requests";
$styles = [];
$styles[] = "request";
$requireLogin = true;
$active = 5;
    include("includes/header.php"); ?>
<div class="main-content container-fluid">
    <div class="row">
            <?php 
                // At this point we know the user is logged in
                // If they are an admin, display all requests
                // If they are a user, display their requests and the form
                // Eeach portion is in it's own file, and php just includes it
                // This makes it easier to do JS and dynamic data rather than echo'd content
                if($_SESSION['user']['isCreator'] == 1 || $_SESSION['user']['isAdmin'] == 1) {
                    include('printRequest.php');
                } else {
                    include('userRequest.php');
                }
            ?>
    </div>
</div>
<?php include("includes/footer.php"); ?>
<script src="/includes/js/request.js"></script>
</html>