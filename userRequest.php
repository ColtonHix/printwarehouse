<?php 
    // this file is a users requests, and the request form
    // ONLY display the contents of this file if the originator was the requests page. Otherwise it shows as nonexistent
    if(isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] != '/requests.php') {
    header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
    header("Location: /notfound");
    }
?>
<div class="col-md">
    <div class="list-group" id="user-requests">
    </div>
</div>
<div class="col-md">
    <h1 class="centered">New Request</h1>
    <form id="request-form" novalidate="true" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Request Name:</label>
            <input class="form-control" type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="reference">Reference Image:</label>
            <input class="form-control" type="file" accept=".jpg, .jpeg, .png" id="reference" name="image"
                value="Upload">
        </div>
        <input class="form-control btn btn-primary" type="submit" id="submitRequest" value="Submit Request">
    </form>
    <div id="newError" class="hidden error-message full-width centered"></div>
</div>
<script>
const rType = "user";
</script>