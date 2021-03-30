<html>
<?php $title = "Print Warehouse - Dashboard"; 
$styles = [];
$styles[] = "request";
$styles[] = "home";
$active = 4;
$requireLogin = true;
    include("includes/header.php"); ?>
<div class="main-content container-fluid">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="product-tab" data-toggle="tab" href="#products" role="tab"
                aria-controls="products" aria-selected="true">Products</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="request-tab" data-toggle="tab" href="#requests" role="tab" aria-controls="requests"
                aria-selected="false">Request List</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="account-tab" data-toggle="tab" href="#account" role="tab" aria-controls="account"
                aria-selected="false">Account Settings</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="new-product-tab" data-toggle="tab" href="#newProduct" role="tab"
                aria-controls="newProduct" aria-selected="false">New Product</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="products" role="tabpanel" aria-labelledby="product-tab">
            <div class="col-md centered">
                <h1>My Products</h1>
                <div class="row" id="user_products">
                    <h2 class="centered full-width">No Prints Found</h2>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="requests" role="tabpanel" aria-labelledby="request-tab">
            <div class="col-md centered">
                <h1>Accepted Requests</h1>
                <div class="list-group" id="accepted-requests">
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="account-tab">
            <div class="col-md">
                <h1 class="centered">My Account</h1>
                <ul style="stats">
                    <li>Username: <?php echo $_SESSION['user']['Username']?></li>
                    <li>Date Affiliated: <?php echo $_SESSION['user']['Registered']?></li>
                </ul>
                <form id="user-info">
                    <label for="email">Email:</label>
                    <input class="full-width" type="text" id="userEmail" disabled
                        value="<?php echo $_SESSION['user']['Email']?>">
                    <label for="password">Password:</label>
                    <input class="full-width" type="password" id="userPassword" value="asasdfasf" disabled>
                    <input class="btn btn-secondary disabled" type="submit" value="Edit Account" disabled></input><span class="error-message"> - We are not allowing account changes at this time.</span>
                </form>
            </div>
        </div>
        <div class="tab-pane fade" id="newProduct" role="tabpanel" aria-labelledby="new-product-tab">
            <div class="col-md">
                <h1 class="centered">New Product</h1>
                <form id="product-form" novalidate="true" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Print Name:</label>
                        <input class="form-control" type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price:</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="dollar">$</span>
                            </div>
                            <input type="number" id="price" name="price" class="form-control" aria-label="Username"
                                aria-describedby="dollar" step="0.01" min="0.01" max="1500.00" required>
                            <div class="invalid-feedback">
                                Price must be between 0.01 and 1500.00
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="reference">Reference Image:</label>
                        <input class="form-control" type="file" accept=".jpg, .jpeg, .png" id="reference" name="image"
                            value="Upload" required>
                    </div>
                    <input class="form-control btn btn-primary" type="submit" id="submitProduct" value="Submit Request">
                </form>
                <div id="newError" class="hidden error-message full-width centered"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="updateModal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginTitle">Edit Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="update-form" novalidate="true" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Print Name:</label>
                        <input class="form-control" id="eName" type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price:</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" id="ePrice" name="price" class="form-control" aria-label="Username"
                                aria-describedby="dollar" step="0.01" min="0.01" max="1500.00" required>
                            <div class="invalid-feedback">
                                Price must be between 0.01 and 1500.00
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" id="eDesc" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="reference">Reference Image:</label>
                        <input class="form-control" type="file" accept=".jpg, .jpeg, .png" id="eReference" name="image"
                            value="Upload">
                    </div>
                    <input type="hidden" id="pId">
                    <input class="form-control btn btn-primary" type="submit"  id="updateProduct" value="Update Product">
                </form>
                <div id="errors" class="hidden error-message"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="deleteModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginTitle">Delete Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>Are you sure you want to delete this product?</h6>
                <h4 class="full-width centered" id="dName"></h4>
                <div id="dError" class="hidden full-width centered error-message"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="delete">Delete</button>
            </div>
        </div>
    </div>
</div>
<script>
const userId = <?php echo $_SESSION['id']?>;
</script>
<?php include("includes/footer.php"); ?>
<script src="/includes/js/dashboard.js"></script>

</html>