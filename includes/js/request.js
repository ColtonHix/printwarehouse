function display(response) {
        if(response['image'] != "") {
            $("#pImage").html(`<img class="max-100" src="${response['image']}">`);
        } else {
            $("#pImage").html(`<div class="no-image"> No reference image provided</div>`);
        }
        $("#product-name").html(response['name']);
        $("#author").html(`By ${response['Username']}`);
        $("#description").html(response['description']);
        // Claimed requests have no button for creators, only their originator
        // Future: add an un-claim method
        if(response['creator'] != "") {
            if($("#requestAction").attr("data-method") == "claim") {
                $("#requestAction").remove();
            }
        }
}

$(document).ready(function() {
    switch (rType) {
        case "all":
            $.ajax({
                method: 'GET',
                url: '/api/request/all/',
                success: function(response) {
                    if(response.length == 0) {
                        $("#request-list").html("<h4 class='full-width centered'>No user requests found!</h4>");
                    } else
                    for(var i=0;i<response.length;i++) {
                        $("#request-list").append(request(response[i]));
                    }
                }
            });
            break;
        case "single":
            const url = new URL(window.location);
            const id = url.pathname.split(/(?<!^)\//)[1];
            $.ajax({
                method: 'GET',
                url: '/api/request/'+id+"/",
                success: (r) => display(r),
                error: function(e) {
                    if(e.status == 404) {
                        $($(".main-content")[0]).html("<h2 class='full-width centered'>Sorry, that request does not exist.</h4>");
                    }
                }
            });
            break;
        case "user":
            $.ajax({
                method: 'GET',
                url: '/api/request/user/',
                success: function(response) {
                    if(response.length == 0) {
                        $("#user-requests").html("<h4 class='full-width centered'>No user requests found!</h4>");
                    } else
                    for(var i=0;i<response.length;i++) {
                        $("#user-requests").append(request(response[i], true));
                    }
                },
                error: function(e) {
                    $("#user-requests").html("<h2 class='full-width centered error-message'>We were unable to retreive your requests. Please try again later.</h4>");
                }
            });
            break;
    }
});

function getRequests(callback) {
    $.ajax({
        url: "/api/request/user/",
        method: 'GET',
        complete: function(response) {
            callback(response);
        }
    });
}

function fillRequests(response) {
    if(response.status == 500) {
        $("#user-requests").html('<h2 class="full-width centered error-message">We were unable to retrieve your requests. Please try again later.</h2>');
    } else {
        $("#user-requests").html("");
        if(response.responseJSON.length == 0) {
            $("#user-requests").html('<h2 class="full-width centered">No requests found.</h2>');
        } else
        for(let i=0; i<response.responseJSON.length;i++) {
            $("#user-requests").append(request(response.responseJSON[i], true));
        }
    }
}

$("#request-form").submit(function(event) {
    event.preventDefault();
    $("#newError").addClass("hidden");
    $("#submitRequest").prop("disabled", true);
    let reVal = true;
    if (!this.checkValidity()) {
        event.preventDefault();
        $("#submitRequest").prop("disabled", false);
    } else {
        $.ajax({
            url: "/api/request/new/",
            method: 'POST',
            processData: false,
            contentType: false,
            async: false,
            data: new FormData(this),
            success: function(response) {
                $("#global-alert").notify("Request Created");
                getRequests((response) => fillRequests(response));
                reVal = false;
                const ins = $("input, textarea").not(":input[type=submit]");
                const inputs = $("#request-form").find(ins);
                $(inputs).each(function() {
                    $(this).val("");
                });
            },
            error: function(response) {
                $("#newError").removeClass("hidden");
                console.log(response);
                if(response.status == 401) {
                    // Again, this should be next to impossible since admins/creators will never even see this page
                    $("#newError").html("You are not allowed to create requests.");
                } else if(response.status == 400) {
                    $("#newError").html("Provided file too large. Ensure file size does not exceed 2 MB.").removeClass("hidden");
                    $("#reference").addClass("not-valid");
                } else {
                    $("#newError").html("There was an error creating your request. Please try again later.");
                }
            },
            complete: function(e) {
                $("#submitRequest").prop("disabled", false);
            }
        });
    }
    if(reVal) $(this).addClass("was-validated");
    else $(this).removeClass("was-validated");
});

$("#requestAction").click(function(event) {
    $(this).prop("disabled", true);
    const url = new URL(window.location);
    const action = $(this).attr("data-method");
    const id = url.pathname.split(/(?<!^)\//)[1];
    $.ajax({
        url: `/api/request/${action}/${id}/`,
        method: (action == "delete" ? 'DELETE' : 'POST'),
        success: function(response) {
            if(action == "claim") {
                $("#global-alert").notify("Request Claimed!");
            } else {
                $("#global-alert").notify("Request Deleted!");
            }
        },
        error: function(response) {
            if(response.status == 401) {
                // Again, this should be next to impossible since admins/creators will never even see this page
                $("#globalAlert").notify("You are not allowed to create requests.", "error");
            } else {
                $("#globalAlert").notify(`Error ${action == "delete" ? "deleting" : "claiming"} request. Please try again later.`, "error");
            }
            $(this).prop("disabled", false);
        }
    });
    
});