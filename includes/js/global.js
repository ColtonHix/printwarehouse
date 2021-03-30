function showRegistration() {
    $("#loginForm").addClass("hidden");
    $("#registerForm").removeClass("hidden");
    $("#registerErrors").addClass("hidden").html("");
    $("#toRegister").addClass("hidden");
    $("#toLogin").removeClass("hidden");
    $("#loginSubmit").html("Register");
    $("#loginTitle").html("Register");
}

function showLogin() {
    $("#loginForm").removeClass("hidden");
    $("#registerForm").addClass("hidden");
    $("#registerErrors").addClass("hidden").html("");
    $("#toRegister").removeClass("hidden");
    $("#toLogin").addClass("hidden");
    $("#loginSubmit").html("Login");
    $("#loginTitle").html("Login");
}
// Nice helper function credit to https://stackoverflow.com/questions/3177836/how-to-format-time-since-xxx-e-g-4-minutes-ago-similar-to-stack-exchange-site
function timeSince(date) {
    const today = new Date();
    date = new Date(date);
    var seconds = Math.floor((today - date) / 1000);
  
    var interval = seconds / 31536000;
  
    if (interval > 1) {
      return Math.floor(interval) + " years ago";
    }
    interval = seconds / 2592000;
    if (interval > 1) {
      return Math.floor(interval) + " months ago";
    }
    interval = seconds / 86400;
    if (interval > 1) {
      return Math.floor(interval) + " days ago";
    }
    interval = seconds / 3600;
    if (interval > 1) {
      return Math.floor(interval) + " hours ago";
    }
    interval = seconds / 60;
    if (interval > 1) {
      return Math.floor(interval) + " minutes ago";
    }
    return Math.floor(seconds) + " seconds ago";
  }
// Event Listeners
function submitForm(event) {
    event.preventDefault();
    if (!this.checkValidity()) {
        event.preventDefault();
    } else {
        $("#registerErrors").addClass("hidden").html("");
        if($(this)[0].id == "registerForm") {
            
            const inputs = $(this).find("input");
            inputs.each((i) => {
                $(inputs[i]).removeClass("taken");
            });
            $.ajax({
                url: "/api/user/new/",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    location.reload();
                },
                error: function(response) {
                    if(response.status == 401) {
                        const username = response.responseJSON['Username'];
                        const email = response.responseJSON['Email'];
                        $("#registerErrors").removeClass("hidden").html(
                            (email > 0 ? "<div>Email is already in use.</div>" : "") +
                            (username > 0 ? "<div>Username is taken.</div>" : "")
                        );
                        if(username > 0) $("#username").addClass("taken");
                        if(email > 0) $("#email").addClass("taken");
                    }
                }
            });
        } else {
            $.ajax({
                url: "/api/user/login/",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    // console.log(response);
                    location.reload();
                },
                error: function(response) {
                    if(response.status == 401) {
                        $("#registerErrors").removeClass("hidden").html("Invalid username or password.");
                    }
                }
            });
        }
    }
    $(this).addClass("was-validated");
}

function validatePass(e) {
    const other = ($(this)[0].id == "password2" ? $("#password") : $("#password2"));
    if($(this).val() && $(this).val() == $(other).val()) {
        $(other).removeClass("is-invalid").addClass("is-valid");
        $(this).removeClass("is-invalid").addClass("is-valid");
    } else {
        $(this).removeClass("is-valid").addClass("is-invalid");
        $(other).removeClass("is-valid").addClass("is-invalid");
    }
}

// This may be used on more pages in the future so It's in global
function getProducts(user, callback) {
    $.ajax({
        url: "/api/product/user/"+user,
        method: 'GET',
        complete: function(response) {
            callback(response);
        }
    });
}
function request(data, user = false) {
    return `<a href="/request/${data['id']}/" class="list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex justify-content-between request-header">
                    <h5 class="mb-1 product-title">${data['name']}${!user ? ` - Requested by ${data['Username']}` : ''}</h5>
                    <small>Created ${timeSince(data['created'])}</small>
                </div>
                <div class="d-flex justify-content-between">
                    <p class="mb-1">${data['description']}</p>
                </div>
            </a>`;
}
// I know the name is misleading, this one fetches all products
// Note to self: change the names of these two functions later
function fetchProducts(callback) {
    $.ajax({
        url: "/api/product/all/",
        method: 'GET',
        complete: function(response) {
            callback(response);
        }
    });
}

$("#password2").on('input', validatePass);
$("#password").on('input', validatePass);

$("#loginForm").submit(submitForm);
$("#registerForm").submit(submitForm);

function validateLogin() {
    var form = $("#loginForm").hasClass("hidden") ? $("#registerForm") : $("#loginForm");
    form.submit();
}

function product(p) {
    return `<div class="product-item col-md-6 col-lg-3 col-xs-6">
    <div class="product">
    <a href="/product/${p['id']}">
        <div class="p-image">
        <div class="center-cropped" style="background-image: url('${p['Image']}')">
        </div></div></a>
        <div class="container">
            <div class="row card-footer">
            <div class="col-md-9"><h4 class="product-name">${p['Name']}</h4></div>
            <div class="col-md-3 product-price">$${p['Price']}</div>
            </div>
        </div></div></div>`;
}

// Wipe all inputs & reset form on modal close
$("#loginModal").on("hidden.bs.modal", function (e) {
    const inputs = $("#loginModal").find("input");
    inputs.each((i) => {
        inputs[i].value = "";
        $(inputs[i]).removeClass("is-valid").removeClass("is-invalid");
    });
    $(this).find("form").removeClass("was-validated");
    $("#registerErrors").addClass("hidden").html("");
    showLogin();
});


jQuery.fn.extend({
    notify: function(text, type = "success") {
        if(type == "error") {
            $("#global-alert").addClass("alert-danger");
        } else {
            $("#global-alert").addClass("alert-success");
        }
        $("#alert-body").html(text);
        $("#global-alert").addClass("show");
        setTimeout(() => $("#global-alert").removeClass('show').removeClass('alert-success').removeClass('alert-danger'), 2500);
    }
});