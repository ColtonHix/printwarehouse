<html>
<?php $title = "Print Warehouse - Sitemap";
$active = 1;
$styles = [];
    $styles[] = "sitemap";
    include("includes/header.php"); ?>
        <div class="main-content">
            <h1>Sitemap</h1>
            <a href="/">Home</a>
            <ul>
                <li><a href="/about.php">About</a></li>
                <li><del><a href="#">Sample Product</a></del> - See a product page instead</li>
                <li><a href="/legal.php">Legal</a></li>
                <li>Sitemap (you are here)</li>
                <li><del><a href="#">Print Request</a></del> - View requests tab as user</li>
                <li><del><a href="#">View Requests</a></del> - View requests tab as admin</li>
                <li><a href="/dashboard.php">Dashboard</a></li>
                <li><del><a href="#">Account</a></del> - Merged with Dashboard</li>
            </ul>
        </div>
    <?php include("includes/footer.php"); ?>
</html>