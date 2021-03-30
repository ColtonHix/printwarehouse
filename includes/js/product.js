function display(product) {
    console.log(product);
    $("#pImage").attr("src", product['Image']);
    $("#product-name").html(product['Name']);
    $("#price").html(`$${product['Price']}`);
    $("#author").html(`By ${product['Username']}`);
    $("#description").html(product['Description']);
}

// Since product pages are pretty much entirely static I could get away with doing the access calls
// in the main PHP page, but I want the option to tell the user something failed (and I already made the API and objects)
$(document).ready(function() {
    const url = new URL(window.location);
    // the product page defines its URL structure as the product id always comes directly after /product/
    // This means we can just use this splitter and grab the second element, everything else is ignored
    const id = url.pathname.split(/(?<!^)\//)[1];
    $.ajax({
        url: "/api/product/"+id+"/",
        method: 'GET',
        success: (r) => display(r),
        error: function(e) {
            if(e.status == 404) {
                $($(".main-content")[0]).html("<h2 class='full-width centered'>Sorry, that product does not exist.</h4>");
            }
        }
    });
});