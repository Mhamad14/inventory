<script>
// orders-Pos system page

if ($("#products_div").length > 0) {
    $(fetch_products());
    $(display_cart());
}
$(".payment_method_name").hide();

function display_products(products, currency) {
    var html = "";
    $.each(products, function (i, products) {
        var product_variants;
        $.each(products["variants"], function (j, variants) {
            // calculate here
            product_variants +=
                '<option value="' +
                variants.id +
                '" data-price="' +
                variants.sale_price +
                '" data-variant_name ="' +
                variants.variant_name +
                '">' +
                variants.variant_name +
                " -" +
                variants.sale_price +
                currency +
                "</option>";
        });
        html =
            '<div class="col-md-4">' +
            '<div class="owl-carousel owl-theme" id="products-carousel">' +
            '<div class="product-item pb-3">' +
            '<div class="item-image">' +
            '<img alt="image" src="' +
            base_url +
            "/" +
            products["image"] +
            '" class="order-image ">  ' +
            "</div>" +
            '<div class="product-details"><div class="product-name">' +
            products["name"] +
            '</div><div class="d-flex justify-content-center">' +
            '<div class="col-md form-group"><label for="product_variant_id">Variant</label><span class="asterisk text-danger"> *</span>' +
            '<select class="form-control product_variants" name="product_variant_id"  id="product_variant_id">' +
            product_variants +
            '</select></div></div><button class="btn btn-xs btn-primary shop-item-button" id ="shop-item-button" data-business_id="' +
            products["business_id"] +
            '" data-tax_id= ' + products["tax_ids"] + ' data-is_tax_included="' +
            products["is_tax_included"] +
            '" data-product_id = "' +
            products["id"] +
            '" onclick="add_to_cart(event)" type="button">Add to Cart</button>' +
            "</div></div></div></div>";
        $("#products_div").append(html);
    });
}
// add to cart

function add_to_cart(e) {
    var cartRow = document.createElement("div");
    cartRow.classList.add("cart-row");
    var button = e.target;
    var product_item = button.parentElement.parentElement;
    var variant_dropdown =
        product_item.children[1].children[1].children[0].children
            .product_variant_id;
    var product_variant_id = variant_dropdown.value;
    var product_id = $(product_item.children[1].children[2]).data("product_id");
    var tax_id = $(product_item.children[1].children[2]).data("tax_id");
    var business_id = $(product_item.children[1].children[2]).data("business_id");
    var is_tax_included = $(product_item.children[1].children[2]).data(
        "is_tax_included"
    );
    var product_name =
        product_item.getElementsByClassName("product-name")[0].innerText;
    var price = $(variant_dropdown.options[variant_dropdown.selectedIndex]).data(
        "price"
    );
    var variant_name = $(
        variant_dropdown.options[variant_dropdown.selectedIndex]
    ).data("variant_name");
    var image = product_item.getElementsByClassName("order-image")[0].src;
    var session_business_id = $("#business_id").val();
    console.log(tax_id);

    var cart_item = {
        product_id: product_id,
        tax_id: tax_id,
        business_id: business_id,
        is_tax_included: is_tax_included,
        product_variant_id: product_variant_id,
        product_name: product_name,
        variant_name: variant_name,
        image: image,
        price: price,
        quantity: 1,
    };
    var cart = localStorage.getItem("cart" + session_business_id);
    cart =
        localStorage.getItem("cart" + session_business_id) !== null ?
            JSON.parse(cart) :
            null;
    if (cart !== null && cart !== undefined) {
        if (cart.find((item) => item.product_variant_id === product_variant_id)) {
            var message = "This item is already present in your cart";
            show_message("Oops!", message, "error");
            return;
        }
        message = "Adding item to cart";
        button.innerText = "adding";
        setTimeout(function () {
            button.innerText = "Add to Cart";
        }, 600);
        cart.push(cart_item);
    } else {
        cart = [cart_item];
    }
    localStorage.setItem("cart" + business_id, JSON.stringify(cart));

    let last_order_id = $("#pos_quick_invoice").data('id');
    if (last_order_id != "") {
        $("#pos_quick_invoice").data('id', "")
        $("#pos_quick_invoice").addClass('d-none');
    }
    display_cart();
    final_total();
}

$(document).on("change", ".cart-quantity-input-new", function (e) {

    this.value = this.value.replace(/[^0-9.]/g, ''); // Allow numbers and a decimal point
    this.value = this.value.replace(/^(\d*\.)(.*)\./g, '$1$2'); // Ensure only one decimal point

    var variant_id = $(this).siblings().val();
    var quantity = $(this).val();
    var data = quantity;

    update_quantity(data, variant_id);

});
function display_cart() {
    var session_business_id = $("#business_id").val();
    var cart = localStorage.getItem("cart" + session_business_id);
    cart =
        localStorage.getItem("cart" + session_business_id) !== null ?
            JSON.parse(cart) :
            null;
    var currency = $(".cart-value").attr("data-currency");
    var cartRowContents = "";
    if (cart !== null && cart.length > 0) {
        cart.forEach((item) => {
            cartRowContents += `
                <div class="container-order">
                    <div class="row ">
                        <div class="col">
                        <div class="cart-image">
                        <a href = "${item.image}" data-lightbox="image-1" > 
                            <img class="mr-4" src="${item.image}">
                        </a></div>
                            <p class="cart-item-title ">${item.variant_name}</p>
                        </div>
                        <div class="col">
                            <span class="cart-price">${currency + parseFloat(item.price).toLocaleString()
                }</span>
                        </div>
                        <div class="col">
                        <div class="input-group-prepend">
                            <input type="hidden" class="product-variant" name="variant_ids[]" type="number" value=${item.product_variant_id
                }>
                            <button type="button" class="cart-quantity-input btn btn-sm btn-secondary" data-operation="minus"><i class="fas fa-minus"></i></button>
                                <input  class="form-control cart-input cart-quantity-input-new text-center p-0" step="0.1" name="quantity[]" id="quantity${item.product_variant_id
                }" data-qty="${item.quantity}"  value="${item.quantity
                }">
                                <button type="button" class="cart-quantity-input btn btn-sm btn-secondary" data-operation="plus"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col">
                            <button class="btn btn-sm btn-danger remove-cart-item" data-business_id=${item.business_id
                } data-variant_id=${item.product_variant_id
                }><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>`;
        });
    } else {
        cartRowContents = `
            <div class="container">
                <div class="row">
                    <div class="col mt-4 d-flex justify-content-center text-primary h5">No items in cart</div>
                </div>
            </div>`;
    }
    $(".cart-items").html(cartRowContents);
    update_cart_total();
}


function cart_total() {
    var session_business_id = $("#business_id").val();
    var cart = localStorage.getItem("cart" + session_business_id);
    cart = cart != null && cart != undefined ? JSON.parse(cart) : null;
    var total = 0;
    if (cart != null && cart != undefined) {
        cart.forEach((item) => {
            var quantity = item.quantity;
            var price = item.price;
            total += quantity * price;
        });
    }
    var currency = $("#cart-total-price").attr("data-currency");
    var total_amont = {
        currency: currency,
        total: total,
        cart_total_formated: parseFloat(total).toLocaleString(),
    };
    return total_amont;
}

function update_cart_total() {
    var total = cart_total();
    var final = final_total();
    $("#cart-total-price").html(total.currency + "" + total.cart_total_formated);
    $("#final_total").html(final.currency + "" + final.cart_total_formated);
    return;
}
$(".final_total").on("keyup", function () {
    final_total();
    update_cart_total();
});
$(".final_total").on("change", function () {
    final_total();
    update_cart_total();
});

function final_total() {
    var cart = cart_total();
    var sub_total = cart.total;
    var discount = $("#discount").val();
    var delivery_charges = $("#delivery_charge").val();
    var final_total = sub_total;
    if (discount != 0 && discount != null) {
        final_total = parseFloat(sub_total) - parseFloat(discount);
    }
    if (delivery_charges != 0 && delivery_charges != null) {
        final_total = parseFloat(final_total) + parseFloat(delivery_charges);
    }
    var currency = $("#final_total").attr("data-currency");
    var res = {
        currency: currency,
        total: final_total,
        cart_total_formated: parseFloat(final_total).toLocaleString(),
    };
    return res;
}

$(document).on("click", ".remove-cart-item", function (e) {
    e.preventDefault();
    var variant_id = $(this).data("variant_id");
    var business_id = $(this).data("business_id");
    $(this).parent().parent().remove();
    var session_business_id = $("#business_id").val();

    var cart = localStorage.getItem("cart" + session_business_id);
    cart =
        localStorage.getItem("cart" + session_business_id) !== null ?
            JSON.parse(cart) :
            null;
    if (cart) {
        var new_cart = cart.filter(function (item) {
            return item.product_variant_id != variant_id;
        });
        localStorage.setItem("cart" + business_id, JSON.stringify(new_cart));
        display_cart();
    }
});

function set_quantity(e) {
    var operation = $(e).data("operation");
    var variant_id = $(e).siblings().val();
    var input = $(e).parent()[0].children[2];
    var qty = parseInt($(input).data("qty"));
    if (operation == "plus") {
        qty = qty + 1;
        $(input).val(qty);
    } else {
        qty = qty - 1;
        $(input).val(qty);
    }
    update_quantity(qty, variant_id);
}

function update_quantity(qty, product_variant_id) {
    if (isNaN(qty) || qty <= 0) {
        qty = 1;
    }
    var session_business_id = $("#business_id").val();
    var cart = localStorage.getItem("cart" + session_business_id);
    cart =
        localStorage.getItem("cart" + session_business_id) !== null ?
            JSON.parse(cart) :
            null;
    if (cart) {
        var i = cart.map((i) => i.product_variant_id).indexOf(product_variant_id);
        cart[i].quantity = qty;
        var business_id = cart[i].business_id;
        localStorage.setItem("cart" + business_id, JSON.stringify(cart));
        display_cart();
    }
}
$(document).on("click", ".cart-quantity-input", function (e) {
    set_quantity(this);
});

$(document).on("click", ".btn-clear_cart", function (e) {
    e.preventDefault();
    delete_cart_items();
});

function delete_cart_items() {
    var session_business_id = $("#business_id").val();
    localStorage.removeItem("cart" + session_business_id);
    display_cart();
}

function fetch_products() {
    var category_id = $("#product_category").find("option:selected").val();
    var brand_id = $("#product_brand").find("option:selected").val();
    var limit = $("input[name=limit]").val();
    var offset = $("input[name=offset]").val();
    var search = $("#search_product").val();
    var flag = null;
    $.ajax({
        type: "GET",
        url: site_url + "admin/products/json",
        cache: false,
        // processData:false,
        data: {
            category_id: category_id,
            brand_id: brand_id,
            search: search,
            limit: limit,
            offset: offset,
        },
        beforeSend: function () {
            $("#products_div").html(
                `<div class="text-center" style='min-height:450px;' ><h4>Please wait.. . loading products..</h4></div>`
            );
        },
        // dataType: "json",
        success: function (result) {
            if (result.error == true) {
                console.log(result.message);
                $("#products_div").html(
                    `<div class="text-center" style='min-height:450px;' ><h4>No products found..</h4></div>`
                );
            } else {
                var products = result.data;
                if (products) {
                    var html = "";
                    $("#total_products").val(result.total);
                    $("#products_div").empty(html);
                    var currency = result.currency;
                    display_products(products, currency);
                    var total = $("#total_products").val();
                    var current_page = $("#current_page").val();
                    var limit = $("#limit").val();
                    paginate(total, current_page, limit);
                }
            }
        },
    });
}

// paginantion
function paginate(total, current_page, limit) {
    var number_of_pages = total / limit;
    var i = 0;
    var pagination = `<div class="row p-2">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            <ul class="pagination mb-0">`;
    pagination += `<li class="page-item disabled"><a class="page-link" href="javascript:prev_page()" tabindex="-1" ><i class="fas fa-chevron-left"></i></a></li>`;
    var active = "";
    while (i < number_of_pages) {
        active = current_page == i ? "active" : "";
        pagination += `<li class="page-item ${active}"><a class="page-link" href="javascript:go_to_page(${limit},${i})">${++i}<span class="sr-only">(current)</span></a></li>`;
    }
    pagination += `<li class="page-item"><a class="page-link" href="javascript:next_page()"><i class="fas fa-chevron-right"></i></a></li>
                </ul>
            </div>
        </div>
    </div>`;

    $(".pagination").html(pagination);
}

function go_to_page(limit, page_number) {
    var total = $("#total_products").val();
    var category_id = $("#product_category").find("option:selected").val();
    var offset = page_number * limit;
    paginate(total, page_number, limit);

    $("#limit").val(limit);
    $("#offset").val(offset);
    $("#current_page").val(page_number);
    fetch_products(category_id, limit, offset);
}

function prev_page() {
    var current_page = $("#current_page").val();
    var limit = $("#limit").val();
    var prev_page = parseFloat(current_page) - 1;

    if (prev_page >= 0) {
        go_to_page(limit, prev_page);
    }
}

function next_page() {
    var current_page = $("#current_page").val();
    var total = $("#total_products").val();
    var limit = $("#limit").val();

    var number_of_pages = total / limit;
    var next_page = parseFloat(current_page) + 1;

    if (next_page < number_of_pages) {
        go_to_page(limit, next_page);
    }
}

$("#product_categories").on("change", function () {
    var category_id = $("#product_categories").val();
    var limit = $("#limit").val();
    $("#current_page").val("0");
    fetch_products(category_id, limit, 0);
});

$("#clear_user_search").on("click", function () {
    $(".select_user").empty();
});

var customer_id = 0;
$(".select_user").on("change", function () {
    customer_id = $(this).val();
});

$(".payment_status").on("change", function () {
    var status = $(this).find("option:selected").val();
    if (status != "partially_paid") {
        $(".amount_paid").hide();
    } else {
        $(".amount_paid").show();
        $(".amount_paid").removeClass("d-none");
    }
});
$(".payment_method").on("click", function () {
    var payment_method = $(this).val();
    if (payment_method == "wallet") {
        $(".amount_paid").hide();
        $(".payment_status").hide();
        $(".payment_status_label").hide();
    } else {
        $(".payment_status_label").show();
        $(".payment_status").show();
        $(".payment_status").trigger("change");
    }
});

// customer registration
$(document).on("submit", "#register_customer", function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);
    $.ajax({
        type: "POST",
        url: this.action,
        dataType: "json",
        data: formData,
        processData: false,
        contentType: false,

        success: function (result) {
            csrf_token = result["csrf_token"];
            csrf_hash = result["csrf_hash"];
            if (result.error == false) {
                location.reload();
            } else {
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: "Error!",
                        message: result.message[key],
                        position: "topRight",
                    });
                });
            }
        },
    });
});
// place order form
function show_message(prefix = "Great!", message, type = "success") {
    Swal.fire(prefix, message, type);
}

$(document).on("ready", function () {
    $(".transaction_id").hide();
    $(".payment_method_name").hide();
});

/* payment method selected event  */
$(".payment_method").on("click", function () {
    var payment_method = $(this).val();
    var exclude_txn_id = ["cash"];
    var include_payment_method_name = ["other"];

    if (exclude_txn_id.includes(payment_method)) {
        $(".transaction_id").hide();
    } else {
        $(".transaction_id").show();
    }

    if (include_payment_method_name.includes(payment_method)) {
        $(".payment_method_name").show();
    } else {
        $(".payment_method_name").hide();
    }
});
$("#place_order_form").on("submit", function (e) {
    e.preventDefault();
    if (confirm("Are you sure? want to check out.")) {
        var session_business_id = $("#business_id").val();
        var cart = localStorage.getItem("cart" + session_business_id);
        if (cart == null || !cart) {
            var message = "Please add items to cart";
            show_message("Oops!", message, "error");
            return;
        }

        var cartTotal = cart_total();
        var total = cartTotal["total"];
        var discount = $("#discount").val();
        var status = $("#status").val();
        var delivery_charges = $("#delivery_charge").val();
        var order_type = $("#order_type").val();
        var message = $("#message").val();
        var finalTotal = final_total();
        var final = finalTotal["total"];
        var payment_status = $("#payment_status_item").find(":selected").val();
        var amount_paid = $("#amount_paid_item").val();
        var payment_method = $(".payment_method:checked").val();
        var transaction_id = $("#transaction_id").val();

        if (payment_status != "unpaid" && payment_status != "cancelled") {
            console.log("here");

            if (!payment_method) {
                var message = "Please choose a payment method";
                show_message("Oops!", message, "error");
                return;
            }
        }
        var payment_method_name = $("#payment_method_name").val();
        if (!payment_method_name) {
            payment_method_name = "";
        }
        const request_body = {
            [csrf_token]: csrf_hash,
            data: cart,
            payment_method: payment_method,
            customer_id: customer_id,
            payment_method_name: payment_method_name,
            total: total,
            discount: discount,
            delivery_charges: delivery_charges,
            final_total: final,
            status: status,
            payment_status: payment_status,
            amount_paid: amount_paid,
            transaction_id: transaction_id,
            order_type: order_type,
            message: message,
        };
        $.ajax({
            type: "post",
            url: this.action,
            data: request_body,
            dataType: "json",
            success: function (result) {
                let order_id = result.data.order_id;
                console.log(order_id);

                csrf_token = result["csrf_token"];
                csrf_hash = result["csrf_hash"];
                if (result.error == true) {
                    var message = "";

                    if (result.message === "Please add order item") {
                        iziToast.error({
                            title: "Error!",
                            message: result.message,
                            position: "topRight",
                        });
                    } else if (
                        result.message === "Amount is more than order total please check!"
                    ) {
                        iziToast.error({
                            title: "Error!",
                            message: result.message,
                            position: "topRight",
                        });
                    } else if (
                        result.message ===
                        "You dont have sufficient wallet balance,Please recharge wallet!"
                    ) {
                        iziToast.error({
                            title: "Error!",
                            message: result.message,
                            position: "topRight",
                        });
                    } else if (result.message === "Please select the customer!") {
                        iziToast.error({
                            title: "Error!",
                            message: result.message,
                            position: "topRight",
                        });
                    } else {
                        Object.keys(result.message).map((key) => {
                            iziToast.error({
                                title: "Error!",
                                message: result.message[key],
                                position: "topRight",
                            });
                        });
                    }
                } else {
                    $("#pos_quick_invoice").data('id', order_id);
                    $("#pos_quick_invoice").removeClass('d-none');
                    // window.location = base_url + "/admin/orders";
                    iziToast.success({
                        title: "Success!",
                        message: result.message,
                        position: "topRight",
                    });
                    delete_cart_items();
                    // setTimeout(function () {
                    //     location.reload();
                    // }, 600);
                }

                get_todays_stats()
            },
        });
    }
});

// create-status form
$("#create_status").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);
    $.ajax({
        type: "post",
        url: this.action,
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (result) {
            csrf_token = result["csrf_token"];
            csrf_hash = result["csrf_hash"];
            if (result.error == true) {
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: "Error!",
                        message: result.message[key],
                        position: "topRight",
                    });
                });
            } else {
                Object.keys(result.message).map((key) => {
                    iziToast.success({
                        title: "Success",
                        message: result.message[key],
                        position: "topRight",
                    });
                });
                update_status_list();
                $("#status_modal").modal('hide');
            }
        },
    });
});



}
// Add these at the top of test.js
$(document).ready(function() {
    console.log("Document ready - initializing cart buttons");
    initCartButtons();
});

function initCartButtons() {
    // Hold Cart button
    $(document).off('click', '#hold_cart_btn').on('click', '#hold_cart_btn', function(e) {
        e.preventDefault();
        console.log("Hold button clicked");
        holdCurrentCart();
    });
    
    // Load Drafts button
    $(document).off('click', '#load_drafts_btn').on('click', '#load_drafts_btn', function(e) {
        e.preventDefault();
        console.log("Load drafts clicked");
        showDraftsModal();
    });
}

// Hold Cart Function
function holdCurrentCart() {
    try {
        const business_id = $("#business_id").val();
        const cart = localStorage.getItem(`cart${business_id}`);
        
        if (cart && JSON.parse(cart).length > 0) {
            const drafts = JSON.parse(localStorage.getItem(`drafts${business_id}`) || "[]");
            const newDraft = {
                id: Date.now(),
                cart: JSON.parse(cart),
                created_at: new Date().toLocaleString()
            };
            
            drafts.push(newDraft);
            localStorage.setItem(`drafts${business_id}`, JSON.stringify(drafts));
            localStorage.removeItem(`cart${business_id}`);
            
            display_cart(); // Refresh cart display
            show_message("Success", "Cart saved as draft", "success");
            return;
        }
        show_message("Error", "No items to save", "error");
    } catch (error) {
        console.error("Hold error:", error);
        show_message("Error", error.message, "error");
    }
}

// Load Drafts Modal
function showDraftsModal() {
    try {
        const business_id = $("#business_id").val();
        const drafts = JSON.parse(localStorage.getItem(`drafts${business_id}`) || "[]");
        
        let modalHTML = `
            <div class="modal fade" id="draftsModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Saved Drafts</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ${drafts.length ? '' : '<p>No saved drafts found</p>'}
                            <div class="list-group">
        `;

        drafts.forEach(draft => {
            modalHTML += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${draft.created_at}</strong><br>
                        ${draft.cart.length} items
                    </div>
                    <div>
                        <button class="btn btn-sm btn-primary me-2" 
                            onclick="loadDraft(${draft.id})">
                            Load
                        </button>
                        <button class="btn btn-sm btn-danger" 
                            onclick="deleteDraft(${draft.id})">
                            Delete
                        </button>
                    </div>
                </div>
            `;
        });

        modalHTML += `
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        $('#draftsModal').remove();
        $('body').append(modalHTML);
        
        // Initialize Bootstrap modal
        const modal = new bootstrap.Modal(document.getElementById('draftsModal'));
        modal.show();
    } catch (error) {
        console.error("Modal error:", error);
        show_message("Error", error.message, "error");
    }
}

// Load Draft Function
function loadDraft(draftId) {
    try {
        const business_id = $("#business_id").val();
        const drafts = JSON.parse(localStorage.getItem(`drafts${business_id}`) || "[]");
        const draft = drafts.find(d => d.id === draftId);

        if (draft) {
            localStorage.setItem(`cart${business_id}`, JSON.stringify(draft.cart));
            display_cart();
            $('#draftsModal').modal('hide');
            show_message("Success", "Draft loaded", "success");
        }
    } catch (error) {
        console.error("Load error:", error);
        show_message("Error", error.message, "error");
    }
}

// Delete Draft Function
function deleteDraft(draftId) {
    if (confirm("Delete this draft permanently?")) {
        try {
            const business_id = $("#business_id").val();
            let drafts = JSON.parse(localStorage.getItem(`drafts${business_id}`) || "[]");
            drafts = drafts.filter(d => d.id !== draftId);
            localStorage.setItem(`drafts${business_id}`, JSON.stringify(drafts));
            $('#draftsModal').modal('hide');
            showDraftsModal();
        } catch (error) {
            console.error("Delete error:", error);
            show_message("Error", error.message, "error");
        }
    }
}
</script>