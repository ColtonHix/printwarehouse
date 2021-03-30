$("#product-form").submit(function(event) {
    event.preventDefault();
    $("#newError").addClass("hidden");
    $("#submitProduct").prop("disabled", true);
    let reVal = true;
    if (!this.checkValidity()) {
        event.preventDefault();
        $("#submitProduct").prop("disabled", false);
    } else {
        const dat = new FormData(this);
        console.log(dat);
        $.ajax({
            url: "/api/product/new/",
            method: 'POST',
            processData: false,
            contentType: false,
            async: false,
            data: new FormData(this),
            success: function(response) {
                $("#global-alert").notify("Product Created");
                getProducts(userId, (response) => fillProducts(response));
                reVal = false;
                const ins = $("input, textarea").not(":input[type=submit]");
                const inputs = $("#product-form").find(ins);
                $(inputs).each(function() {
                    $(this).val("");
                });
            },
            error: function(response) {
                $("#newError").removeClass("hidden");
                if(response.status == 401) {
                    $("#newError").html("You are not allowed to create products.");
                } else if(response.status == 400) {
                    $("#newError").html("Provided file too large. Ensure file size does not exceed 2 MB.");
                    $("#reference").val("");
                } else {
                    $("#newError").html("There was an error creating your product. Please ensure file does not exceed 2 MB and try again later.");
                }
            },
            complete: function(e) {
                $("#submitProduct").prop("disabled", false);
            }
        });
    }
    if(reVal) $(this).addClass("was-validated");
    else $(this).removeClass("was-validated");
});

$("#delete").click(function() {
    $(this).prop("disabled", true);
    $("#dError").addClass("hidden");
    $.ajax({
        url: "/api/product/delete/"+$(this).attr("data-id"),
        method: 'DELETE',
        success: function(response) {
            $("#global-alert").notify("Product Deleted");
            getProducts(userId, (response) => fillProducts(response));
            $("#deleteModal").modal('hide');
        },
        error: function(response) {
            $("#dError").removeClass("hidden");
            if(response.status == 401) {
                // This shouldn't ever happen with server side & client side validation but just in case
                $("#dError").html("You cannot delete this product.");
            } else {
                $("#dError").html("Error deleting product. Please try again later.");
            }
        }
    });
    $(this).prop("disabled", false);
});

$("#update-form").submit(function(event) {
    event.preventDefault();
    $("#errors").addClass("hidden");
    $("#updateProduct").prop("disabled", true);
    if (!this.checkValidity()) {
        event.preventDefault();
        $("#updateProduct").prop("disabled", false);
    } else {
        $.ajax({
            url: "/api/product/update/"+$("#pId").val()+"/",
            method: 'POST',
            processData: false,
            contentType: false,
            data: new FormData(this),
            async: false, // Images tend to not get uplaoded sometimes when async is enabled
            success: function(response) {
                $("#global-alert").notify("Product Updated");
                getProducts(userId, (response) => fillProducts(response));
                $("#updateModal").modal('hide');
                $("#updateProduct").prop("disabled", false);
            },
            error: function(response) {
                if(response.status == 401) {
                    // This shouldn't ever happen with server side & client side validation but just in case
                    $("#errors").removeClass("hidden").html("You cannot update this product.");
                } else if(response.status == 400) {
                    $("#errors").html("Provided file too large. Ensure file size does not exceed 2 MB.").removeClass("hidden");
                    $("#eReference").addClass("not-valid");
                } else {
                    $("#errors").removeClass("hidden").html("Error updating product. Please ensure file does not exceed 2 MB and try again later.");
                }
            },
            complete: function(e) {
                $("#updateProduct").prop("disabled", false);
            }
        });
    }
    $(this).addClass("was-validated");
});

function updateModal(p) {
    $("#eName").val(p['Name']);
    $("#eDesc").val(p['Description']);
    $("#ePrice").val(p['Price']);
    $("#pId").val(p['id']);
    $("#updateModal").modal("show");
}

function deleteModal(p) {
    $("#dName").html(p['Name']);
    $("#delete").attr("data-id", p['id']);
    $("#deleteModal").modal("show");
}

function userProduct(p) {
    return `<div class="product-item col-md-6 col-xl-3 col-xs-6 col-lg-4">
    <div class="product">
    <a href="/product/${p['id']}">
        <div class="p-image">
        <div class="center-cropped" style="background-image: url('${p['Image']}')">
        </div></div></a>
        <div class="container">
            <div class="row">
            <div class="col-md-9"><h4 class="product-name">${p['Name']}</h4></div>
            <div class="col-md-3 product-price">$${p['Price']}</div>
            </div>
                <div class="row card-footer">
                <div class="col"><button type="button" data-purpose="update" class="full-width btn btn-primary">Edit</button></div>
                <div class="col"><button type="button" data-purpose="delete" class="full-width btn btn-secondary">Delete</button></div>
                </div>
        </div></div></div>`;
}

function fillProducts(response) {
    if(response.status == 500) {
        $("#user_products").html('<h2 class="full-width centered error-message">We were unable to retrieve your products. Please try again later.</h2>');
    } else {
        $("#user_products").html("");
        if(response.responseJSON.length == 0) {
            $("#user_products").html('<h2 class="full-width centered">No products found.</h2>');
        } else
        for(let i=0; i<response.responseJSON.length;i++) {
            $("#user_products").append(userProduct(response.responseJSON[i]));
            $("#user_products").find("button[data-purpose=update]").last().click(() => updateModal(response.responseJSON[i]));
            $("#user_products").find("button[data-purpose=delete]").last().click(() => deleteModal(response.responseJSON[i]));
        }
    }
}
function toPrice(e) {
    $(e.target).val(parseFloat($(e.target).val()).toFixed(2));
}
function getRequests(callback) {
    $.ajax({
        url: "/api/request/accepted/",
        method: 'GET',
        complete: function(response) {
            callback(response);
        }
    });
}
function fillRequests(response) {
    if(response.status == 500) {
        $("#accepted-requests").html('<h2 class="full-width centered error-message">We were unable to retrieve your requests. Please try again later.</h2>');
    } else {
        $("#accepted-requests").html("");
        if(response.responseJSON.length == 0) {
            $("#accepted-requests").html('<h2 class="full-width centered">No requests found.</h2>');
        } else
        for(let i=0; i<response.responseJSON.length;i++) {
            $("#accepted-requests").append(request(response.responseJSON[i]));
        }
    }
}
$("#price").change((e) => toPrice(e));
$("#ePrice").change((e) => toPrice(e));
$("#updateModal").on("hidden.bs.modal", function (e) {
    const ins = $("input, textarea").not(":input[type=submit]");
    const inputs = $("#updateModal").find(ins);
    inputs.each((i) => {
        inputs[i].value = "";
        $(inputs[i]).removeClass("is-valid").removeClass("is-invalid");
    });
    $(this).find("form").removeClass("was-validated");
    $("#errors").addClass("hidden");
});
$(document).ready(function() {
    getProducts(userId, (response) => fillProducts(response));
    getRequests((response) => fillRequests(response));
});