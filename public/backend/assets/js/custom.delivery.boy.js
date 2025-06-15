/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";
console.log("register_customer_form");

// order-details update status of ordered item 
$(function () {
    var path = window.location.pathname;
    path = path.replace(/\/$/, "");
    path = decodeURIComponent(path);
    path = document.location.href;

    $(".sidebar-menu li a").each(function () {
        var href = $(this).attr("href");
        if (href === path) {
            $('.navbar li a').removeClass('active')
            $(this).closest("li").addClass("active");
            if ($(this).parents().hasClass('dropdown-menu')) {
                $(this).parents().addClass('active')
                $(this).parents().show();
            }
        }
    });
});

function update_status(e) {
    var status = $(e).find('option:selected').val();
    var order_id = $(e).find(":selected").attr("data-order_id");
    var type = $(e).find(":selected").attr("data-type");

    $.ajax({
        type: "get",
        url: site_url + '/delivery_boy/orders/update_order_status',
        data: {
            status: status,
            order_id: order_id,
            type: type
        },
        cache: false,
        dataType: 'json',
        success: function (result) {
            if (result.error == false) {
                showToastMessage(result.message, result.type)
                location.reload()

            } else {
                showToastMessage(result.message, result.type)
            }
        }
    });

}
$(".status_update").on('change', function () {
    update_status(this);
});


function show_message(prefix = "Great!", message, type = 'success') {
    Swal.fire(prefix, message, type);
}
// bulk status update of order items
var item_id = [];

function update_bulk_status(item_id) {
    var bulk_status;
    var type;
    bulk_status = $(".status_bulk").find('option:selected').val();
    type = $(".status_bulk").attr("data-type");
    if (bulk_status == "" || bulk_status == 0) {
        var message = "Please select status for bulk update!";
        show_message("Oops!", message, "error");
        return;
    }
    if (item_id == "" || item_id == undefined) {
        var message = "Please check item for bulk update!";
        show_message("Oops!", message, "error");
        return;
    }
    var response = [item_id, bulk_status, type];
    return response
}
$(".update_status_bulk ").on("click", function (e) {
    e.preventDefault();
    var item = update_bulk_status(item_id);
    if (!item) {
        return;
    } else {
        var item_ids = item[0];
        var status = item[1];
        var type = item[2];
        const request_body = {
            [csrf_token]: csrf_hash,
            item_ids: item_ids,
            status: status,
            type: type,
        }
        $.ajax({
            type: "post",
            url: base_url + '/delivery_boy/orders/update_status_bulk',
            data: request_body,
            cache: false,
            dataType: 'json',
            success: function (result) {
                csrf_token = result['csrf_token'];
                csrf_hash = result['csrf_hash'];
                if (result.error == true) {
                    showToastMessage(result.message, result.type)
                } else {
                    showToastMessage(result.message, result.type)
                    location.reload()
                }
            }
        });
    }
});

$(function () {
    $('.status_order_bulk').on("click", function () {
        if (this.checked) {
            var checked = $('.status_order').prop('checked', this.checked)
            $.each(checked, function (i, checked) {
                var id = checked.value;
                item_id.push(id);
            });
        } else {
            var checked = $('.status_order').prop('checked', false)
            item_id = [];
        }

    });
    $('.status_order').on("click", function () {
        if (this.checked) {
            var id = $(this).val();
            item_id.push(id);
            $('.status_order_bulk').prop('checked', false)

        } else {
            var id = $(this).val();
            item_id.pop(id);
        }
    })
});

// delivery boy pos system 

$(fetch_products());
$(display_cart());
$('.payment_method_name').hide();

function fetch_products() {
    var category_id = $('#product_category').find('option:selected').val();
    var limit = $('input[name=limit]').val();
    var offset = $('input[name=offset]').val();
    var search = $("#search_product").val();

    $.ajax({
        type: "GET",
        url: site_url + '/admin/products/json',
        cache: false,
        data: {
            category_id: category_id,
            search: search,
            limit: limit,
            offset: offset
        },
        beforeSend: function () {
            $("#products_div").html(`<div class="text-center" style='min-height:450px;' ><h4>Please wait.. . loading products..</h4></div>`);
        },
        dataType: 'json',
        success: function (result) {
            if (result.error == true) {
                console.log(result.message);
                $("#products_div").html(`<div class="text-center" style='min-height:450px;' ><h4>No products found..</h4></div>`);

            } else {

                var products = result.data;
                if (products) {
                    var html = "";
                    $("#total_products").val(result.total);
                    $('#products_div').empty(html);
                    display_products(products);
                    var total = $("#total_products").val();
                    var current_page = $("#current_page").val();
                    var limit = $("#limit").val();
                    paginate(total, current_page, limit);
                }
            }
        }
    });
}

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

// function add_to_cart(e) {
//     var cartRow = document.createElement('div');
//     cartRow.classList.add('cart-row');
//     var button = e.target;
//     var product_item = button.parentElement.parentElement;
//     var variant_dropdown = product_item.children[1].children[1].children[0].children.product_variant_id;
//     var product_variant_id = variant_dropdown.value;
//     var product_id = $(product_item.children[1].children[2]).data("product_id");
//     var tax_id = $(product_item.children[1].children[2]).data("tax_id");
//     var business_id = $(product_item.children[1].children[2]).data("business_id");
//     var is_tax_included = $(product_item.children[1].children[2]).data("is_tax_included");
//     var product_name = product_item.getElementsByClassName('product-name')[0].innerText;
//     var price = $(variant_dropdown.options[variant_dropdown.selectedIndex]).data("price");
//     var variant_name = $(variant_dropdown.options[variant_dropdown.selectedIndex]).data("variant_name");
//     var image = product_item.getElementsByClassName('order-image')[0].src;
//     var session_business_id = $("#business_id").val();
//     var cart_item = {
//         "product_id": product_id,
//         "tax_id": tax_id,
//         "business_id": business_id,
//         "is_tax_included": is_tax_included,
//         "product_variant_id": product_variant_id,
//         "product_name": product_name,
//         "variant_name": variant_name,
//         "image": image,
//         "price": price,
//         "quantity": 1
//     };
//     var cart = localStorage.getItem("delivery_boy_cart" + session_business_id);
//     cart = (localStorage.getItem("delivery_boy_cart" + session_business_id) !== null) ? JSON.parse(cart) : null;
//     if (cart !== null && cart !== undefined) {
//         if (cart.find((item) => item.product_variant_id === product_variant_id)) {
//             var message = "This item is already present in your cart"
//             show_message("Oops!", message, "error");
//             return;
//         }
//         message = "Adding item to cart";
//         button.innerText = "adding"
//         setTimeout(function () {
//             button.innerText = "Add to Cart"
//         }, 600);
//         cart.push(cart_item);
//     } else {
//         cart = [cart_item];
//     }
//     localStorage.setItem("delivery_boy_cart" + business_id, JSON.stringify(cart));

//     display_cart();
//     final_total();
// }
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
    var cart = localStorage.getItem("delivery_boy_cart" + session_business_id);
    cart =
        localStorage.getItem("delivery_boy_cart" + session_business_id) !== null ?
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
    localStorage.setItem("delivery_boy_cart" + business_id, JSON.stringify(cart));

    display_cart();
    final_total();
}

function display_cart() {
    var session_business_id = $("#business_id").val();
    var cart = localStorage.getItem("delivery_boy_cart" + session_business_id);
    cart = (localStorage.getItem("delivery_boy_cart" + session_business_id) !== null) ? JSON.parse(cart) : null;
    var currency = $(".cart-value").attr('data-currency');
    var cartRowContents = "";
    if (cart !== null && cart.length > 0) {
        cart.forEach((item) => {
            cartRowContents += `
                <div class="container-order">
                    <div class="row ">
                        <div class="col">
                        <div class="cart-image">
                            <img class="mr-4" src="${item.image}">
                        </div>
                            <p class="cart-item-title ">${item.variant_name}</p>
                        </div>
                        <div class="col">
                            <span class="cart-price">${currency + parseFloat(item.price).toLocaleString()}</span>
                        </div>
                        <div class="col">
                        <div class="input-group-prepend">
                            <input type="hidden" class="product-variant" name="variant_ids[]" type="number" value=${item.product_variant_id}>
                            <button type="button" class="cart-quantity-input btn btn-sm btn-secondary" data-operation="minus"><i class="fas fa-minus"></i></button>
                                <input class="form-control cart-input text-center p-0" name="quantity[]" id="quantity${item.product_variant_id}" data-qty="${item.quantity}" value="${item.quantity}">
                                <button type="button" class="cart-quantity-input btn btn-sm btn-secondary" data-operation="plus"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col">
                            <button class="btn btn-sm btn-danger remove-cart-item" data-business_id=${item.business_id} data-variant_id=${item.product_variant_id}><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>`
        })
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
    var cart = localStorage.getItem("delivery_boy_cart" + session_business_id);
    cart = (cart != null && cart != undefined) ? JSON.parse(cart) : null;
    var total = 0;
    if (cart != null && cart != undefined) {
        cart.forEach((item) => {
            var quantity = item.quantity;
            var price = item.price;
            total += (quantity * price);

        });
    }
    var currency = $('#cart-total-price').attr('data-currency');
    var total_amont = {
        "currency": currency,
        "total": total,
        "cart_total_formated": parseFloat(total).toLocaleString()
    }
    return total_amont;
}

function update_cart_total() {
    var total = cart_total();
    var final = final_total();
    $('#cart-total-price').html(total.currency + "" + total.cart_total_formated);
    $('#final_total').html(final.currency + "" + final.cart_total_formated);
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
    var currency = $('#final_total').attr('data-currency');
    var res = {
        "currency": currency,
        "total": final_total,
        "cart_total_formated": parseFloat(final_total).toLocaleString()
    }
    return res;
}

$(document).on("click", ".remove-cart-item", function (e) {
    e.preventDefault();
    var variant_id = $(this).data("variant_id");
    var business_id = $(this).data("business_id");
    $(this).parent().parent().remove();
    var session_business_id = $("#business_id").val();

    var cart = localStorage.getItem("delivery_boy_cart" + session_business_id);
    cart = (localStorage.getItem("delivery_boy_cart" + session_business_id) !== null) ? JSON.parse(cart) : null;
    if (cart) {
        var new_cart = cart.filter(function (item) {
            return item.product_variant_id != variant_id
        });
        localStorage.setItem("delivery_boy_cart" + business_id, JSON.stringify(new_cart));
        display_cart();
    }
});

function set_quantity(e) {
    var operation = $(e).data("operation");
    var variant_id = $(e).siblings().val();
    var input = $(e).parent()[0].children[2];
    var qty = parseInt($(input).data("qty"));
    if (operation == "plus") {
        qty = (qty == 10) ? 10 : qty + 1;
        $(input).val(qty)
    } else {
        qty = qty - 1;
        $(input).val(qty)
    }
    update_quantity(qty, variant_id);
}

function update_quantity(qty, product_variant_id) {
    if (isNaN(qty) || qty <= 0) {
        qty = 1;
    }
    var session_business_id = $("#business_id").val();
    var cart = localStorage.getItem("delivery_boy_cart" + session_business_id);
    cart = (localStorage.getItem("delivery_boy_cart" + session_business_id) !== null) ? JSON.parse(cart) : null;
    if (cart) {
        var i = cart.map(i => i.product_variant_id).indexOf(product_variant_id);
        cart[i].quantity = qty;
        var business_id = cart[i].business_id;
        localStorage.setItem("delivery_boy_cart" + business_id, JSON.stringify(cart));
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
    localStorage.removeItem("delivery_boy_cart" + session_business_id);
    display_cart();
}

// pagination 
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
        active = (current_page == i) ? "active" : "";
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
    var category_id = $('#product_category').find('option:selected').val();
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

$('#product_categories').on("change", function () {
    var category_id = $('#product_categories').val();
    var limit = $('#limit').val();
    $('#current_page').val("0");
    fetch_products(category_id, limit, 0);
});

$("#clear_user_search").on('click', function () {
    $(".select_user").empty();
});

var customer_id = 0;
$('.select_user').on('change', function () {
    customer_id = ($(this).val());
});

function show_message(prefix = "Great!", message, type = 'success') {
    Swal.fire(prefix, message, type);
}

$(document).on("ready", function () {
    $('.transaction_id').hide();
    $('.payment_method_name').hide();
});

/* payment method selected event  */
$(".payment_method").on('click', function () {
    var payment_method = $(this).val();
    var exclude_txn_id = ["cash"];
    var include_payment_method_name = ["other"];

    if (exclude_txn_id.includes(payment_method)) {
        $(".transaction_id").hide();
    } else {
        $(".transaction_id").show();
    }

    if (include_payment_method_name.includes(payment_method)) {
        $('.payment_method_name').show();
    } else {
        $('.payment_method_name').hide();
    }
});
$(".transaction_id").hide();
$('#payment_mode').on("change", function () {
    var type = $(this).find('option:selected').val();
    if (type == "other") {
        var html = ' <label for="payment_method_name">Enter Payment Method Name</label><span class="asterisk text-danger"> *</span>' +
            '<input type="text" class="form-control" id="payment_method_name" name="payment_method_name" placeholder="">';
        $('#type').append(html);
        $(".transaction_id").show();

    } else if (type == "cash") {
        $(".transaction_id").hide();
    } else {
        $('#type').html("");
        $(".transaction_id").show();
    }
});
$('.payment_status').on("change", function () {
    var status = $(this).find('option:selected').val();
    if (status != "partially_paid") {
        $('.amount_paid').hide();
    } else {
        $('.amount_paid').show();
        $('.amount_paid').removeClass('d-none');
    }

});
$('.payment_method').on("click", function () {
    var payment_method = $(this).val();
    if (payment_method == "wallet") {
        $('.amount_paid').hide();
        $(".payment_status").hide();
        $(".payment_status_label").hide();
    } else {
        $(".payment_status_label").show();
        $(".payment_status").show();
        $('.payment_status').trigger("change");
    }
});


$("#product_wallet").on("change", function () {
    var user_id = $(this).val();
    $.ajax({
        type: "get",
        url: site_url + "/admin/orders/customer_balance",
        data: {
            user_id: user_id,
        },
        cache: false,
        dataType: "json",
        success: function (result) {
            if (result.error == false) {
                var balance = result.balance;
                $("#wallet_balance").html("");
                $("#wallet_balance").append("wallet balance:" + balance + "₹");
            } else {
                iziToast.error({
                    title: "Error!",
                    message: result.message,
                    position: "topRight",
                });
                location.reload();
            }
        },
    });
});
// customer registration
$(document).on('submit', '#register_customer_form', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);
    $.ajax({
        type: 'POST',
        url: this.action,
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,

        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            if (result.error == false) {
                location.reload()
                iziToast.success({
                    title: 'Success!',
                    message: result.message,
                    position: 'topRight'
                });
                document.getElementById('register_customer_form').reset();
            } else {
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message[key],
                        position: 'topRight'
                    });
                });
            }
        }
    });
});

$('#place_order_form').on('submit', function (e) {
    e.preventDefault();
    if (confirm('Are you sure? want to check out.')) {
        var session_business_id = $("#business_id").val();
        var cart = localStorage.getItem("delivery_boy_cart" + session_business_id);
        if (cart == null || !cart) {
            var message = "Please add items to cart";
            show_message("Oops!", message, "error");
            return;
        }

        var cartTotal = cart_total();
        var total = cartTotal['total'];
        var discount = $('#discount').val();
        var status = $('#status').val();
        var delivery_charges = $('#delivery_charge').val();
        var order_type = $('#order_type').val();
        var message = $("#message").val();
        var finalTotal = final_total();
        var final = finalTotal['total'];
        var payment_status = $('#payment_status_item').find(":selected").val();
        var amount_paid = $('#amount_paid_item').val();
        var payment_method = $('.payment_method:checked').val();
        var transaction_id = $("#transaction_id").val();

        if (payment_status != "unpaid" && payment_status != "cancelled") {
            console.log("here");
            if (!payment_method) {
                var message = "Please choose a payment method";
                show_message("Oops!", message, "error");
                return;
            }
        }
        var payment_method_name = $('#payment_method_name').val();
        if (!payment_method_name) {
            payment_method_name = '';
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
            message: message
        }

        $.ajax({
            type: "post",
            url: this.action,
            data: request_body,
            dataType: 'json',
            success: function (result) {
                csrf_token = result['csrf_token'];
                csrf_hash = result['csrf_hash'];
                if (result.error == true) {
                    var message = "";

                    if (result.message === "Please add order item") {
                        iziToast.error({
                            title: 'Error!',
                            message: result.message,
                            position: 'topRight'
                        });
                    } else if (result.message === "Amount is more than order total please check!") {
                        iziToast.error({
                            title: 'Error!',
                            message: result.message,
                            position: 'topRight'
                        });
                    } else if (result.message === "You dont have sufficient wallet balance,Please recharge wallet!") {
                        iziToast.error({
                            title: 'Error!',
                            message: result.message,
                            position: 'topRight'
                        });
                    } else if (result.message === "Please select the customer!") {
                        iziToast.error({
                            title: 'Error!',
                            message: result.message,
                            position: 'topRight'
                        });
                    } else {

                        Object.keys(result.message).map((key) => {
                            iziToast.error({
                                title: 'Error!',
                                message: result.message[key],
                                position: 'topRight'
                            });
                        });
                    }
                } else {
                    window.location = base_url + '/delivery_boy/orders';
                    iziToast.success({
                        title: 'Success!',
                        message: result.message,
                        position: 'topRight'
                    });
                    delete_cart_items();
                    setTimeout(function () {
                        location.reload();
                    }, 600);
                }
            }
        });
    }
});

// create order payment
$(document).on('show.bs.modal', '#create_payment', function (event) {
    var triggerElement = $(event.relatedTarget);
    var current_selected_variant = triggerElement;
    var order_id = $(current_selected_variant).data('order_id');
    var customer_id = $(current_selected_variant).data('customer_id');

    $('input[name="order_id"]').val(order_id);
    $('input[name="customer_id"]').val(customer_id);
});

$('.create_order_payment').on('submit', function (e) {
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
        dataType: 'json',
        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            if (result.error == true) {
                $("#create_payment").find('.modal-body').prepend('<div class="alert alert-danger">' + result['message'] + '</div>')
                $("#create_payment").find('.alert-danger').delay(4000).fadeOut();
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message[key],
                        position: 'topRight'
                    });
                });
            } else {
                setTimeout(function () {
                    location.reload();
                }, 600);
                iziToast.success({
                    title: 'Success!',
                    message: result.message,
                    position: 'topRight'
                });
            }
        }
    });
});


var start_date = "";
var end_date = "";
var payment_status_filter = "";
var order_type_filter = "";
$('#payment_status_filter').on('change', function () {
    payment_status_filter = $(this).find('option:selected').val();

});

$('#order_type_filter').on('change', function () {
    order_type_filter = $(this).find('option:selected').val();

});
$(function () {
    $('input[name="date_range"]').daterangepicker({
        opens: 'left'
    }, function (start, end) {
        start_date = start.format('YYYY-MM-DD');
        end_date = end.format('YYYY-MM-DD');
    });
});
$('#date_range').on('change', function () { });

function orders_query(p) {
    return {
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        start_date: start_date,
        end_date: end_date,
        payment_status_filter: payment_status_filter,
        order_type_filter: order_type_filter,
    };
}


$('#filter').on('click', function (e) {

    $('#orders_table').bootstrapTable('refresh');
});

// customer wallet transaction
$('#customers_table').on('check.bs.table', function (e, row) {
    $('#name').val(row.customer_name);
    $('#email').val(row.email);
    $('#customer_id').val(row.id);
});


/* Search AJAX Users in POS */
$(document).ready(function () {
    $(".select_user").select2({
        ajax: {
            url: site_url + "admin/orders/get_users",
            dataType: "json",
            data: function (params) {
                return { search: params.term };
            },
            processResults: function (response) {
                console.log(response);
                return {
                    results: response.data.map(user => ({
                        id: user.id,
                        text: user.text,  // Ensure 'text' exists in response
                        number: user.number || "N/A",
                        email: user.email || "N/A"
                    }))
                };
            },
            cache: true,
        },
        placeholder: "Search for a User",
        templateResult: formatPost,
        templateSelection: formatPostSelection,
    });
});

function formatPost(post) {
    if (post.loading) {
        return post.text;
    }

    var $container = $(
        `<div class="select2-result-user clearfix">
            <div class="select2-result-user__meta">
                <strong>${post.text}</strong> | 
                <strong>${post.number}</strong> | 
                <strong>${post.email}</strong>
            </div>
        </div>`
    );

    return $container;
}

function formatPostSelection(post) {
    console.log(post.text);
    return post.text || post.id;
}


if ($("#myChart").length > 0) {
    var total_sale = [];
    var month_name;
    var data = [];

    $.ajax({
        type: "get",
        url: site_url + '/delivery_boy/home/fetch_sales',
        cache: false,
        dataType: 'json',
        success: function (result) {
            total_sale = result.total_sale
            month_name = result.month_name
            var data = {
                labels: month_name,
                datasets: [{
                    label: 'My First dataset',
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(201, 203, 207, 0.2)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                    borderWidth: 1,
                    data: total_sale,
                }]
            };

            var config = {
                type: 'bar',
                data: data,
                options: {}
            };
            var myChart = new Chart(
                document.getElementById('myChart'),
                config
            );

        }
    });

}

function set_locale(language_code) {
    $.ajax({
        url: base_url + "/admin/languages/change/" + language_code,
        type: "GET",
        success: function (result) {

        }
    }).then(() => {
        location.reload();
    });
}



document.getElementById('calculatorIcon').addEventListener('click', function () {
    const dropdown = document.getElementById('dropdownCalculator');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});

// Calculator functions
function appendValue(value) {
    document.getElementById('display').value += value;
}

function clearDisplay() {
    document.getElementById('display').value = '';
}

function calculateResult() {
    const display = document.getElementById('display');
    try {
        display.value = eval(display.value); // Evaluate the expression
    } catch (error) {
        display.value = 'Error'; // If there's an error in the expression
    }
}

// Backspace function to remove the last character
function backspace() {
    const display = document.getElementById('display');
    display.value = display.value.slice(0, -1); // Remove the last character
}

// Prevent the calculator from closing when clicking inside the calculator area
document.getElementById('dropdownCalculator').addEventListener('click', function (event) {
    event.stopPropagation(); // Prevent event bubbling
});

// Close the dropdown when clicking outside the calculator or the icon
document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('dropdownCalculator');
    const calculatorIcon = document.getElementById('calculatorIcon');
    if (!calculatorIcon.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});