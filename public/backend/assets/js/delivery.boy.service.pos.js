/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

$(fetch_services());
$(display_service_cart());
$('.payment_method_name_service').hide();
var starts_on;
var ends_on;
var renewable_status = "";

$("#services-tab").on("click", () => {
    $('.renew_date').daterangepicker({
        "singleDatePicker": true,
        "alwaysShowCalendars": true,
        "startDate": moment(),
        "endDate": "03/25/2022  ",
        locale: {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "cancelLabel": 'Clear',
            'label': 'Select range of dates to filter'

        },
    });
    $('.renew_date').attr({
        'placeholder': ' Select Renew Date Range ',
        'autocomplete': 'off'
    });

    $('.renew_date').on('apply.daterangepicker', function (ev, picker) {
        var drp = $(this).data('daterangepicker');
        var id = $(this).attr("data-id");
        var recurring_days = $(this).attr("data-recurring_days");
        var starts_on = drp.startDate.format('YYYY-MM-DD');
        var ends_on = moment(starts_on, "YYYY-MM-DD").add(recurring_days, 'days');
        ends_on = ends_on.format('YYYY-MM-DD');
        $('#starts_on' + id).val(starts_on);
        $('#ends_on' + id).val(ends_on);
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });

    $('.renew_date').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        var id = $(this).attr("data-id");
        $('#starts_on' + id).val('');
        $('#ends_on' + id).val('');
    });
});


function fetch_services() {
    var limit = $('input[name=limit_service]').val();
    var offset = $('input[name=offset_service]').val();
    var search = $("#search_service").val();

    $.ajax({
        type: "GET",
        url: site_url + '/admin/services/json',
        cache: false,
        data: {
            search: search,
            limit: limit,
            offset: offset
        },
        beforeSend: function () {
            $("#services_div").html(`<div class="text-center" style='min-height:450px;' ><h4>Please wait.. . loading services..</h4></div>`);
        },
        dataType: 'json',
        success: function (result) {
            if (result.error == true) {
                console.log(result.message);
                $("#services_div").html(`<div class="text-center" style='min-height:450px;' ><h4>No services found..</h4></div>`);

            } else {

                var services = result.data;
                if (services) {
                    var html = "";
                    $("#total_services").val(result.total);
                    $('#services_div').empty(html);
                    display_services(services);
                    var total = $("#total_services").val();
                    var current_page = $("#current_page_service").val();
                    var limit = $("#limit_service").val();
                    paginate_services(total, current_page, limit);
                }
            }
        }
    });
}

function display_services(services) {
    var html = "";

    $.each(services, function (i, services) {
        var ribbon = "";
        var renewable_dates = "";
        if (services['is_recursive'] == 1 && services['is_recursive'] != null) {
            ribbon = '<div class="item-image container-ribbon" data-ribbon="renewable">';
            renewable_dates = `<input data-id="${services['id']}" data-recurring_days="${services['recurring_days']}" type="text" name="renew_date" class="form-control renew_date"></div>
                                  <input type="hidden" id="starts_on${services['id']}" value="" name="starts_on">
                                  <input type="hidden" id="ends_on${services['id']}" value="" name="ends_on">`
        } else {
            ribbon = '<div class="item-image">';
            renewable_dates = '';
        }
        html = '<div class="col-md-4 mb-3">' +
            '<div class="owl-carousel owl-theme" id="products-carousel">' +
            '<div class="product-item">' +
            ribbon +
            '<img alt="image" src="' + base_url + "/" + services['image'] + '" class="order-image " >' +
            '</div>' +
            '<div class="product-details"><div class="product-name" data-unit_id = ' + services['unit_id'] + ' data-unit_name = ' + services['unit_name'] + '  data-cost_price = ' + services['cost_price'] + ' data-price = ' + services['price'] + '>' + services['name'] + '</div><div class="d-flex justify-content-center">' +
            '<label>' + services['unit_name'] + - +services['price'] + '₹</label>' +
            '</div>' + `<span id="span${services['id']}"> ${renewable_dates} </span>` +
            '<button class="btn btn-xs btn-primary shop-item-button m-2" id = "shop-item-button" data-recurring_days="' + services['recurring_days'] + '" data-is_recursive="' + services['is_recursive'] + '" data-business_id="' + services['business_id'] + '" data-tax_id= "' + services['tax_id'] + '" data-is_tax_included="' + services['is_tax_included'] + '" data-service_id = "' + services['id'] + '" onclick="add_to_cart_service(event)" type="button">Add to Cart</button>' +
            '</div></div></div>'
        $('#services_div').append(html);
    });
}

function add_to_cart_service(e) {
    var cartRow = document.createElement('div');
    cartRow.classList.add('cart-row');
    var button = e.target;
    var is_recursive = $(button).attr("data-is_recursive")
    var recurring_days = $(button).attr("data-recurring_days")
    var business_id = $(button).attr("data-business_id");
    var tax_id = $(button).attr("data-tax_id")
    var is_tax_included = $(button).attr("data-is_tax_included")
    var service_id = $(button).attr("data-service_id")
    var service_item = button.parentElement.parentElement;
    var service_name = service_item.getElementsByClassName('product-name')[0].innerText;
    var price = service_item.getElementsByClassName('product-name')[0].dataset.price;
    var cost_price = service_item.getElementsByClassName('product-name')[0].dataset.cost_price;
    var unit_name = service_item.getElementsByClassName('product-name')[0].dataset.unit_name;
    var unit_id = service_item.getElementsByClassName('product-name')[0].dataset.unit_id;
    var session_business_id = $("#business_id").val();
    var image = service_item.getElementsByClassName('order-image')[0].src;
    var starts_date;
    var ends_date;
    if (is_recursive == "1") {
        starts_date = service_item.children[0].children[2].value;
        ends_date = service_item.children[0].children[3].value;
    } else {
        starts_date = "";
        ends_date = "";
    }
    var cart_item = {
        "business_id": business_id,
        "service_id": service_id,
        "service_name": service_name,
        "price": price,
        "quantity": 1,
        "unit_name": unit_name,
        "unit_id": unit_id,
        "tax_id": tax_id,
        "is_tax_included": is_tax_included,
        "is_recursive": is_recursive,
        "cost_price": cost_price,
        "recurring_days": recurring_days,
        "image": image,
        "starts_on": starts_date,
        "ends_on": ends_date,
    };
    var cart_service = localStorage.getItem("delivery_boy_cart_service" + session_business_id);
    cart_service = (localStorage.getItem("delivery_boy_cart_service" + session_business_id) !== null) ? JSON.parse(cart_service) : null;
    if (cart_service !== null && cart_service !== undefined) {
        if (cart_service.find((item) => item.service_id === service_id)) {
            var message = "This item is already present in your cart"
            show_message("Oops!", message, "error");
            return;
        }
        message = "Adding item to cart";
        show_message("yaay!", message, "success");
        cart_service.push(cart_item);
    } else {
        cart_service = [cart_item];
    }
    localStorage.setItem("delivery_boy_cart_service" + business_id, JSON.stringify(cart_service));

    display_service_cart();
    final_total_service();
}

function display_service_cart() {
    var session_business_id = $("#business_id").val();
    var cart_service = localStorage.getItem("delivery_boy_cart_service" + session_business_id);
    cart_service = (localStorage.getItem("delivery_boy_cart_service" + session_business_id) !== null) ? JSON.parse(cart_service) : null;
    var currency = $(".cart-value").attr('data-currency');
    var cartRowContents = "";
    if (cart_service !== null && cart_service.length > 0) {
        cart_service.forEach((item) => {
            var qty_input;
            var end_date;
            var quantity;
            if (item['is_recursive'] && item['is_recursive'] == "1") {
                qty_input = `<small>Auto Renewable</small>`
                quantity = ''
                end_date = `<span class="cart-price" id="end">${item.ends_on}</span>`

            } else {
                qty_input = `<input type ="number" min="1" class="form-control cart-input cart-quantity-input-service text-center p-0" name="quantity[]" data-recurring_days="${item.recurring_days}" data-start_date="${item.starts_on}" data-end_date="${item.ends_on}" id="quantity${item.service_id}" data-id="${item.service_id}" data-qty="${item.quantity}" value="${item.quantity}">`
                quantity = `<label class="unit-cart-label" for="quantity${item.service_id}">${item.unit_name}</label>`
                end_date = ''
            }
            cartRowContents += `
                <div class="container-order">
                    <div class="row">
                        <div class="col">
                        <div class="cart-image">
                            <img class="mr-4" src="${item.image}">
                        </div>
                            <p class="cart-item-title ">${item.service_name}</p>
                        </div>
                        <div class="col">
                            <span class="cart-price">${currency + parseFloat(item.price).toLocaleString()}</span>
                        </div>
                        <div class="col">
                        <div class="input-group-prepend">
                            <input type="hidden" class="product-variant" name="variant_ids[]" type="number" value=${item.service_id}>
                            ${qty_input}
                            ${quantity}
                            </div>
                        </div>
                        <div class="col">
                        <span class="cart-price">${item.starts_on}</span>
                    </div>
                    <div class="col">
                       ${end_date}
                    </div>
                        <div class="col">
                            
                            <button class="btn btn-sm btn-danger remove-cart-item_1" data-business_id=${item.business_id} data-service_id=${item.service_id}><i class="fas fa-trash"></i></button>
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
    $(".cart-services").html(cartRowContents);
    update_cart_total_service();
}


function cart_total_service() {
    var session_business_id = $("#business_id").val();
    var cart_service = localStorage.getItem("delivery_boy_cart_service" + session_business_id);
    cart_service = (cart_service != null && cart_service != undefined) ? JSON.parse(cart_service) : null;
    var total = 0;
    if (cart_service != null && cart_service != undefined) {
        cart_service.forEach((item) => {
            var quantity = item.quantity;
            var price = item.price;
            total += (quantity * price);

        });
    }
    var currency = $('#cart-total-price-service').attr('data-currency');
    var total_amont = {
        "currency": currency,
        "total": total,
        "cart_service_total_formated": parseFloat(total).toLocaleString()
    }
    return total_amont;
}


function update_cart_total_service() {
    var total = cart_total_service();
    var final = final_total_service();
    $('#cart-total-price-service').html(total.currency + "" + total.cart_service_total_formated);
    $('#final_total_service').html(final.currency + "" + final.cart_service_total_formated);
    return;
}
$(".final_total_service").on("keyup", function () {
    final_total_service();
    update_cart_total_service();
});
$(".final_total_service").on("change", function () {
    final_total_service();
    update_cart_total_service();
});

function final_total_service() {
    var cart_service = cart_total_service();
    var sub_total = cart_service.total;
    var discount = $("#discount_service").val();
    var delivery_charges = $("#delivery_charge_service").val();
    var final_total_service = sub_total;
    if (discount != 0 && discount != null) {
        final_total_service = parseFloat(sub_total) - parseFloat(discount);
    }
    if (delivery_charges != 0 && delivery_charges != null) {
        final_total_service = parseFloat(final_total_service) + parseFloat(delivery_charges);

    }
    var currency = $('#final_total_service').attr('data-currency');
    var res = {
        "currency": currency,
        "total": final_total_service,
        "cart_service_total_formated": parseFloat(final_total_service).toLocaleString()
    }
    return res;
}

$(document).on("click", ".remove-cart-item_1", function (e) {
    e.preventDefault();
    var service_id = $(this).data("service_id");
    var business_id = $(this).data("business_id");
    $(this).parent().parent().remove();
    var session_business_id = $("#business_id").val();

    var cart_service = localStorage.getItem("delivery_boy_cart_service" + session_business_id);
    cart_service = (localStorage.getItem("delivery_boy_cart_service" + session_business_id) !== null) ? JSON.parse(cart_service) : null;
    if (cart_service) {
        var new_cart = cart_service.filter(function (item) {
            return item.service_id != service_id
        });
        localStorage.setItem("delivery_boy_cart_service" + business_id, JSON.stringify(new_cart));
        display_service_cart();
    }
});

function update_quantity_service(qty, service_id, end = "") {
    if (isNaN(qty) || qty <= 0) {
        qty = 1;
    }
    var session_business_id = $("#business_id").val();
    var cart_service = localStorage.getItem("delivery_boy_cart_service" + session_business_id);
    cart_service = (localStorage.getItem("delivery_boy_cart_service" + session_business_id) !== null) ? JSON.parse(cart_service) : null;
    if (cart_service) {
        var i = cart_service.map(i => i.service_id).indexOf(service_id);
        cart_service[i].quantity = qty;
        if (end) {
            cart_service[i].ends_on = end;
        }
        var business_id = cart_service[i].business_id;
        localStorage.setItem("delivery_boy_cart_service" + business_id, JSON.stringify(cart_service));
        display_service_cart();
    }
}
$(document).on("change", ".cart-quantity-input-service", function (e) {
    var qty = $(this).val();
    var service_id = $(this).attr("data-id")
    var days = $(this).attr("data-recurring_days");
    var starts_on = $(this).attr("data-start_date");
    var a = qty * days;
    var ends_on = moment(starts_on, "YYYY-MM-DD").add(a, 'days');
    ends_on = ends_on.format('YYYY-MM-DD');
    update_quantity_service(qty, service_id, ends_on);
});
$(document).on("keyup", ".cart-quantity-input-service", function (e) {
    var qty = $(this).val();
    var service_id = $(this).attr("data-id")
    var days = $(this).attr("data-recurring_days");
    var starts_on = $(this).attr("data-start_date");
    var a = qty * days;
    var ends_on = moment(starts_on, "YYYY-MM-DD").add(a, 'days');
    ends_on = ends_on.format('YYYY-MM-DD');
    update_quantity_service(qty, service_id, ends_on);
});

$(document).on("click", ".btn-clear_cart", function (e) {
    e.preventDefault();
    delete_cart_service();
});

function delete_cart_service() {
    var session_business_id = $("#business_id").val();
    localStorage.removeItem("delivery_boy_cart_service" + session_business_id);
    display_service_cart();
}


// paginantion
function paginate_services(total, current_page, limit) {
    var number_of_pages = total / limit;
    var i = 0;
    var pagination = `<div class="row p-2">
     <div class="col-12">
         <div class="d-flex justify-content-center">
             <ul class="pagination mb-0">`;
    pagination += `<li class="page-item disabled"><a class="page-link" href="javascript:prev_page_service()" tabindex="-1" ><i class="fas fa-chevron-left"></i></a></li>`;
    var active = "";
    while (i < number_of_pages) {
        active = (current_page == i) ? "active" : "";
        pagination += `<li class="page-item ${active}"><a class="page-link" href="javascript:go_to_page_service(${limit},${i})">${++i}<span class="sr-only">(current)</span></a></li>`;
    }
    pagination += `<li class="page-item"><a class="page-link" href="javascript:next_page_service()"><i class="fas fa-chevron-right"></i></a></li>
                 </ul>
             </div>
         </div>
     </div>`;

    $(".pagination_services").html(pagination);
}

function go_to_page_service(limit, page_number) {
    var total = $("#total_services").val();
    var offset = page_number * limit;
    paginate_services(total, page_number, limit);

    $("#limit_service").val(limit);
    $("#offset_service").val(offset);
    $("#current_page_service").val(page_number);
    fetch_services(limit, offset);
}

function prev_page_service() {
    var current_page = $("#current_page_service").val();
    var limit = $("#limit_service").val();
    var prev_page_service = parseFloat(current_page) - 1;

    if (prev_page_service >= 0) {
        go_to_page_service(limit, prev_page_service);
    }
}

function next_page_service() {
    var current_page = $("#current_page_service").val();
    var total = $("#total_services").val();
    var limit = $("#limit_service").val();

    var number_of_pages = total / limit;
    var next_page_service = parseFloat(current_page) + 1;

    if (next_page_service < number_of_pages) {
        go_to_page_service(limit, next_page_service);
    }
}


$("#clear_user_search").on('click', function () {
    $(".select_user").empty();
});

// place order form
$("#service_wallet").on("change", function () {
    var user_id = $(this).val();
    $.ajax({
        type: "get",
        url: site_url + '/admin/orders/customer_balance',
        data: {
            user_id: user_id,
        },
        cache: false,
        dataType: 'json',
        success: function (result) {
            if (result.error == false) {
                var balance = result.balance;
                $('#wallet_balance_service').html("");
                $('#wallet_balance_service').append("wallet balance:" + balance + "₹");
            } else {
                iziToast.error({
                    title: 'Error!',
                    message: result.message,
                    position: 'topRight'
                });
                location.reload()
            }
        }
    });
});

function show_message(prefix = "Great!", message, type = 'success') {
    Swal.fire(prefix, message, type);
}
$(".payment_method_service").on('click', function () {
    var payment_method = $(this).val();
    var include_payment_method_name = ["other"];

    if (include_payment_method_name.includes(payment_method)) {
        $('.payment_method_name_service').show();
    } else {
        $('.payment_method_name_service').hide();
    }
});

$('.payment_status_service').on("change", function () {
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
        $(".payment_status_service").hide();
        $(".payment_status_label_service").hide();
    } else {
        $(".payment_status_label_service").show();
        $(".payment_status_service").show();
        $('.payment_status_service').trigger("change");
    }
});

$('#place_service_order_form').on('submit', function (e) {
    e.preventDefault();
    if (confirm('Are you sure? want to check out.')) {
        var session_business_id = $("#business_id").val();
        var cart_service = localStorage.getItem("delivery_boy_cart_service" + session_business_id);
        if (cart_service == null || !cart_service) {
            var message = "Please add items to cart";
            show_message("Oops!", message, "error");
            return;
        }
        var cartTotal = cart_total_service();
        var total = cartTotal['total'];
        var discount = $('#discount_service').val();
        var status = $('#service_status').val();
        var delivery_charges = $('#delivery_charge_service').val();
        var payment_status = $('#payment_status').find(":selected").val();
        var amount_paid = $('#amount_paid').val();
        var order_type = $('#order_type_service').val();
        var delivery_charges = $('#delivery_charge_service').val();
        var message = $("#service_message").val();
        var finalTotal = final_total_service();
        var final = finalTotal['total'];
        var payment_method = $('.payment_method_service:checked').val();
        var transaction_id = $("#transaction_id_service").val();
        if (!payment_method) {
            var message = "Please choose a payment method";
            show_message("Oops!", message, "error");
            return;
        }
        var payment_method_name = $('#payment_method_name_service').val();
        if (!payment_method_name) {
            payment_method_name = '';
        }
        const request_body = {
            [csrf_token]: csrf_hash,
            data: cart_service,
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
                    } else if (result.message === "Please Add Wallet Balance") {
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
                    delete_cart_service();
                    setTimeout(function () {
                        location.reload();
                    }, 600);
                }
            }
        });
    }
});

$('.payment_method_service').on("click", function () {
    var payment_method = $(this).val();
    if (payment_method == "wallet") {
        $(".payment_status_label_service").hide();
        $(".payment_status_service").hide();
        $('.amount_paid').hide();
    } else {
        $(".payment_status_label_service").show();
        $(".payment_status_service").show();
        $('.payment_status').trigger("change");
    }
});

$(document).on("ready", function () {
    $('.transaction_id_service').hide();
    $('.payment_method_name_service').hide();
});

/* payment method selected event  */
$(".payment_method_service").on('click', function () {
    var payment_method = $(this).val();
    var exclude_txn_id = ["cash"];
    var include_payment_method_name = ["other"];

    if (exclude_txn_id.includes(payment_method)) {
        $(".transaction_id_service").hide();
    } else {
        $(".transaction_id_service").show();
    }

    if (include_payment_method_name.includes(payment_method)) {
        $('.payment_method_name_service').show();
    } else {
        $('.payment_method_name_service').hide();
    }
});