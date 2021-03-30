<html>
    <?php $title = "Print Warehouse";
    $styles = [];
    $styles[] = "home";
    include("includes/header.php"); ?>
        <div class="container-fluid main-content">
            <div class="row">
                <p class="slider">
                this is a sample body text that may not even exist in the final design (probably not). Pretty much everything here is using flex as a placeholder for Bootstrap.
                For example, the product list below can be created using a pagination plugin. Everything here probably isn't mobile/resize friendly.
                The colors may change in the future, but perhaps not so I can see everything (<em>without going blind</em>).
                I'm not really bothering to add full static slider HTML since it will all be replaced with JS and Bootstrap later, so pretend this text box is the slider. Here, I'll even put 
                a border on it.
                </p>
                <hr/>
            </div>
            <div class="row" id="product-list">
            </div>
        </div>
    <?php include("includes/footer.php"); ?>
    <script src="/includes/js/index.js"></script>
</html>