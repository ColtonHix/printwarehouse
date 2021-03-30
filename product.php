<html>
<?php $title = "Print Warehouse"; 
$styles = [];
$requireId = true;
$styles[] = "product";
    include("includes/header.php");?>
<div class="main-content container-fluid">
    <div class="row">
        <div class="col-md-6">
            <img id="pImage">
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
                <?php 
                    if(!isset($_SESSION['id']))
                        echo '<button type="button" class="btn btn-secondary full-width" disabled>You must be logged in to purchase products.</button>';
                    else
                        echo '<a class="btn btn-primary full-width" href="/purchase">Purchase</a>';
                ?>
                
            </div>
        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>
<script src="/includes/js/product.js"></script>

</html>