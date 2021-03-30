<?php 
    // Name is a bit misleading; this file is ALL print requests, visible to admins & creators
    // ONLY display the contents of this file if the originator was the requests page. Otherwise it shows as nonexistent
    if(isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] != '/requests.php') {
    header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
    header("Location: /notfound");
    }
?>
<div class="col-md" id="inner-content">
    <div class="list-group" id="request-list">
    </div>
</div>
<script>
    const rType = "all";
</script>