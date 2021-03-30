<html>
<?php $title = "Print Warehouse";
$requireLogin = true;
// User must be able to create or be the originator of this request
$requireId = true;
$requireOriginCreator = true;
$styles = [];
$styles[] = "product";
$styles[] = "request";
    include("includes/header.php");?>
<div class="main-content container-fluid">
    <div class="row">
        <div class="col-md-6" id="pImage">
        </div>
        <div class="col-md-6">
            <div class="container">
                <div class="row">
                    <div class="col-md-9">
                        <h1 id="product-name"></h1>
                    </div>
                    <h2 class="col-md-3" id="price"></h2>
                </div>
                <h2 class="col-md-12" id="author"></h2>
                <div class="section">
                    <h3>Description:</h3>
                    <div class="col-md-12" id="description"></div>
                </div>
                <?php  // By now we have verified the user can view the request. If they are not an admin, they are the creator
                    if(intval($_SESSION['user']['isCreator']) != 1 && intval($_SESSION['user']['isAdmin']) != 1)
                        echo '<button type="button" class="btn btn-secondary full-width" data-method="delete" id="requestAction">Delete Request</button>';
                    else
                        echo '<button type="button" class="btn btn-primary full-width" data-method="claim" id="requestAction">Claim Request</button>';
                ?>
                
            </div>
        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>
<script>const rType = "single";</script>
<script src="/includes/js/request.js"></script>

</html>