function fillProducts(response) {
    if(response.status == 500) {
        $("#product-list").html('<h2 class="full-width centered error-message">We were unable to retrieve the products. Please try again later.</h2>');
    } else {
        $("#product-list").html("");
        if(response.responseJSON.length == 0) {
            $("#product-list").html('<h2 class="full-width centered">No products found.</h2>');
        } else
        for(let i=0; i<response.responseJSON.length;i++) {
            $("#product-list").append(product(response.responseJSON[i]));
        }
    }
}
$(document).ready(function() {
    $(document).ready(fetchProducts((response) => fillProducts(response)));
});