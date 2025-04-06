/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";
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
            $(this).closest("li .nav-item .dropdown").addClass("active");
            if ($(this).parents().hasClass('dropdown-menu')) {
                $(this).parents().addClass('active')
                $(this).parents().show();
            }

        }
    });
});

let userId = $("#user_id").val();

if (jQuery().summernote) {
    $(".summernote").summernote({
        dialogsInBody: true,
        minHeight: 250,
    });
    $(".summernote-simple").summernote({
        dialogsInBody: true,
        minHeight: 150,
        toolbar: [
            ["style", ["bold", "italic", "underline", "clear"]],
            ["font", ["strikethrough"]],
            ["para", ["paragraph"]],
        ],
    });
}
tinymce.init({
    selector: '.texteditor',
    height: "480"
});


// Login form 
$('#login_form').on('submit', function (e) {

    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);
    $.ajax({
        type: "post",
        url: this.action,
        data: formData,
        beforeSend: function () {
            $('#login_btn').html('Please Wait..');
            $('#login_btn').attr('disabled', true);
        },
        cache: false,
        processData: false,
        contentType: false,
        dataType: 'json',

        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            if (result.error == true) {
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message[key],
                        position: 'topRight'
                    });
                });
            } else {
                if (result.vendor == true) {
                    showToastMessage(result.message, "success")
                    setTimeout(function () {
                        location.href = base_url + "/admin/home";
                    }, 500);
                }
                if (result.admin == true) {
                    showToastMessage(result.message, "success")
                    setTimeout(function () {
                        location.href = base_url + "/admin/home";
                    }, 500);

                }
                if (result.delivery_boy == true) {
                    showToastMessage(result.message, "success")
                    setTimeout(function () {
                        location.href = base_url + "/delivery_boy/home";
                    }, 500);
                }
            }
        }
    });

});

$(document).on('click', '.remove_tenure', function (e) {
    e.preventDefault();
    $(this).parent().parent().remove();
});
$('#create_package_form').on('submit', function (e) {
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

                $('#create_package_result').show();
                $('#create_package_result').html(result.message);
                $('#create_package_result').html(result.message.title);
                $('#create_package_result').html(result.message.no_of_businesses);
                $('#create_package_result').html(result.message.no_of_delivery_boys);
                $('#create_package_result').html(result.message.no_of_products);
                $('#create_package_result').html(result.message.no_of_customers);
                $('#create_package_result').html(result.message.description);
                $('#create_package_result').html(result.message.tenure);
                $('#create_package_result').html(result.message.price);
            } else {
                $('#create_package_result').hide();
                window.location = base_url + '/admin/packages';
            }
        }
    });
});
// Edit Package-form
$(document).ready(function () {
    if ($('#edit_package_form').length > 0) {
        var tenures = $('#tenures').val();
        if (tenures) {
            tenures = JSON.parse(tenures);
            var html = "";
            $.each(tenures, function (i, tenure) {

                html = '<div class="tenure-item py-1"><div class="row"><div class="col-md-3 custom-col">' +
                    '<input type="text" class="form-control" class="tenure" name="tenure[]" placeholder="Ex.Monthly,Quarterly,Yearly" value="' + tenure['tenure'] + '" required></div>' +
                    '<div class="col-md-3 custom-col">' +
                    '<select class="form-control" class="months" name="months[]" value="' + tenure['months'] + '" required>' +
                    ' <option value="">Select Months</option>' +
                    '<option value="1" ' + ((tenure['months'] == 1) ? "selected" : "") + '>1</option><option value="2" ' + ((tenure['months'] == 2) ? "selected" : "") + '>2</option><option value="3" ' + ((tenure['months'] == 3) ? "selected" : "") + '>3</option><option value="4" ' + ((tenure['months'] == 4) ? "selected" : "") + '>4</option>' +
                    '<option value="5" ' + ((tenure['months'] == 5) ? "selected" : "") + '>5</option><option value="6" ' + ((tenure['months'] == 6) ? "selected" : "") + '>6</option><option value="7" ' + ((tenure['months'] == 7) ? "selected" : "") + '>7</option><option value="8" ' + ((tenure['months'] == 8) ? "selected" : "") + '>8</option><option value="9" ' + ((tenure['months'] == 9) ? "selected" : "") + '>9</option><option value="10" ' + ((tenure['months'] == 10) ? "selected" : "") + '>10</option>' +
                    '<option value="11" ' + ((tenure['months'] == 11) ? "selected" : "") + '>11</option><option value="12" ' + ((tenure['months'] == 12) ? "selected" : "") + '>12</option><option value="13" ' + ((tenure['months'] == 13) ? "selected" : "") + '>13</option><option value="14" ' + ((tenure['months'] == 14) ? "selected" : "") + '>14</option><option value="15" ' + ((tenure['months'] == 15) ? "selected" : "") + '>15</option><option value="16" ' + ((tenure['months'] == 16) ? "selected" : "") + '>16</option>' +
                    '<option value="17" ' + ((tenure['months'] == 17) ? "selected" : "") + '>17</option><option value="18" ' + ((tenure['months'] == 18) ? "selected" : "") + '>18</option><option value="19" ' + ((tenure['months'] == 19) ? "selected" : "") + '>19</option><option value="20" ' + ((tenure['months'] == 20) ? "selected" : "") + '>20</option><option value="21" ' + ((tenure['months'] == 21) ? "selected" : "") + '>21</option><option value="22" ' + ((tenure['months'] == 22) ? "selected" : "") + '>22</option>' +
                    '<option value="23" ' + ((tenure['months'] == 23) ? "selected" : "") + '>23</option><option value="24" ' + ((tenure['months'] == 24) ? "selected" : "") + '>24</option><option value="25" ' + ((tenure['months'] == 25) ? "selected" : "") + '>25</option><option value="26" ' + ((tenure['months'] == 26) ? "selected" : "") + '>26</option><option value="27" ' + ((tenure['months'] == 27) ? "selected" : "") + '>27</option><option value="28" ' + ((tenure['months'] == 28) ? "selected" : "") + '>28</option>' +
                    '<option value="29" ' + ((tenure['months'] == 29) ? "selected" : "") + '>29</option><option value="30" ' + ((tenure['months'] == 30) ? "selected" : "") + '>30</option><option value="31" ' + ((tenure['months'] == 31) ? "selected" : "") + '>31</option><option value="32" ' + ((tenure['months'] == 32) ? "selected" : "") + '>32</option><option value="33">33</option><option value="34" ' + ((tenure['months'] == 34) ? "selected" : "") + '>34</option>' +
                    '<option value="35" ' + ((tenure['months'] == 35) ? "selected" : "") + '>35</option><option value="36" ' + ((tenure['months'] == 36) ? "selected" : "") + '>36</option></select></div>' +
                    '<div class="col-md-2 custom-col"><input type="number" class="form-control" class="price" name="price[]" min="0.00" placeholder="0.00" value="' + tenure['price'] + '" required>' +
                    '</div><div class="col-md-2 custom-col"><input type="number" class="form-control" class="discounted_price" name="discounted_price[]" min="0" value="' + tenure['discounted_price'] + '" placeholder="0.00"></div>' +
                    ' <div class="col-md-1 custom-col"><button class="btn btn-icon btn-danger remove-tenure-item remove_tenure" data-tenure_id="' + tenure['id'] + '" name="remove_tenure"><i class="fas fa-trash"></i></button></div>' +
                    '<input type="hidden" name="tenure_id[]" id="tenure_id"  value="' + tenure['id'] + '">'
                '</div></div>';
                $('#tenures_div').append(html);
            });
        }
    }
    $(document).on("click", "#add_tenure", function (e) {
        e.preventDefault();
        validate_tenure();
    });

    function validate_tenure() {
        var tenure = $('#tenure').val();
        var price = $('#price').val();
        var months = $('#months').val();
        var discounted_price = $('#discounted_price').val();
        //Ajax post
        if (tenure == null || tenure == "") {
            iziToast.error({
                title: 'Error!',
                message: "Tenure cannot be blank",
                position: 'topRight'
            });
            return;
        } else if (price == null || price == "") {
            iziToast.error({
                title: 'Error!',
                message: "Price cannot be blank",
                position: 'topRight'
            });
            return;
        } else {
            html = '<div class="tenure-item py-1"><div class="row"><div class="col-md-3 custom-col">' +
                '<input type="text" class="form-control" class="tenure" name="tenure[]" placeholder="Ex.Monthly, Quarterly, Yearly" value="' + tenure + '" required></div>' +
                '<div class="col-md-3 custom-col">' +
                '<select class="form-control" class="months" name="months[]" required>' +
                ' <option value="">Select Months</option>' +
                '<option value="1" ' + ((months == 1) ? "selected" : "") + '>1</option><option value="2" ' + ((months == 2) ? "selected" : "") + '>2</option><option value="3" ' + ((months == 3) ? "selected" : "") + '>3</option><option value="4" ' + ((months == 4) ? "selected" : "") + '>4</option>' +
                '<option value="5" ' + ((months == 5) ? "selected" : "") + '>5</option><option value="6" ' + ((months == 6) ? "selected" : "") + '>6</option><option value="7" ' + ((months == 7) ? "selected" : "") + '>7</option><option value="8" ' + ((months == 8) ? "selected" : "") + '>8</option><option value="9" ' + ((months == 9) ? "selected" : "") + '>9</option><option value="10" ' + ((months == 10) ? "selected" : "") + '>10</option>' +
                '<option value="11" ' + ((months == 11) ? "selected" : "") + '>11</option><option value="12" ' + ((months == 12) ? "selected" : "") + '>12</option><option value="13" ' + ((months == 13) ? "selected" : "") + '>13</option><option value="14" ' + ((months == 14) ? "selected" : "") + '>14</option><option value="15" ' + ((months == 15) ? "selected" : "") + '>15</option><option value="16" ' + ((months == 16) ? "selected" : "") + '>16</option>' +
                '<option value="17" ' + ((months == 17) ? "selected" : "") + '>17</option><option value="18" ' + ((months == 18) ? "selected" : "") + '>18</option><option value="19" ' + ((months == 19) ? "selected" : "") + '>19</option><option value="20" ' + ((months == 20) ? "selected" : "") + '>20</option><option value="21" ' + ((months == 21) ? "selected" : "") + '>21</option><option value="22" ' + ((months == 22) ? "selected" : "") + '>22</option>' +
                '<option value="23" ' + ((months == 23) ? "selected" : "") + '>23</option><option value="24" ' + ((months == 24) ? "selected" : "") + '>24</option><option value="25" ' + ((months == 25) ? "selected" : "") + '>25</option><option value="26" ' + ((months == 26) ? "selected" : "") + '>26</option><option value="27" ' + ((months == 27) ? "selected" : "") + '>27</option><option value="28" ' + ((months == 28) ? "selected" : "") + '>28</option>' +
                '<option value="29" ' + ((months == 29) ? "selected" : "") + '>29</option><option value="30" ' + ((months == 30) ? "selected" : "") + '>30</option><option value="31" ' + ((months == 31) ? "selected" : "") + '>31</option><option value="32" ' + ((months == 32) ? "selected" : "") + '>32</option><option value="33">33</option><option value="34" ' + ((months == 34) ? "selected" : "") + '>34</option>' +
                '<option value="35" ' + ((months == 35) ? "selected" : "") + '>35</option><option value="36" ' + ((months == 36) ? "selected" : "") + '>36</option></select></div>' +
                '<div class="col-md-2 custom-col"><input type="number" class="form-control" class="price" name="price[]" min="0" placeholder="0.00" value="' + price + '" required>' +
                '</div><div class="col-md-2 custom-col"><input type="number" class="form-control" class="discounted_price" name="discounted_price[]" min="0.00" value="' + discounted_price + '" placeholder="0.00"></div>' +
                ' <div class="col-md-1 custom-col"><button class="btn btn-icon btn-danger remove-tenure-item remove_tenure" name="remove_tenure"><i class="fas fa-trash"></i></button></div>' +
                '<input type="hidden" class="remove_tenure" name="tenure_id[]" id="tenure_id" placeholder="" value="">'
            '</div></div></div>';
            $('#tenures_div').append(html);
            $('#tenure').val('');
            $('#price').val('');
            $('#months').val('');
            $('#discounted_price').val('');
        }
    }

    $(document).on('click', '.remove_tenure', function (e) {
        e.preventDefault();
        if (!confirm("Are you sure want to delete?")) {
            return false;
        }
        e.stopPropagation();
        e.stopImmediatePropagation();
        var tenure_id = $(this).attr("data-tenure_id");
        $.ajax({
            type: "get",
            url: site_url + '/admin/packages/remove_tenure/' + tenure_id,
            cache: false,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (result) {
                if (result.error == false) {
                    iziToast.success({
                        title: 'Success!',
                        message: result.message,
                        position: 'topRight'
                    });
                } else {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message,
                        position: 'topRight'
                    });
                }
            }
        });
        $(this).parent().parent().parent().remove();
    });
});

function delete_plan(element) {
    if (!confirm("Are you sure you want to delete this plan?")) {
        return false;
    }
    var plan_id = $(element).data("plan-id");
    let req_body = {
        [csrf_token]: csrf_hash,
        plan_id: plan_id,
    };
    $.ajax({
        url: base_url + "/admin/packages/delete_plan",
        type: "POST",
        data: req_body,
        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            if (result.error) {
                showToastMessage(result.message, "error");
                3;
                return;
            } else {
                window.location.reload();
                showToastMessage(result.message, "success");
                3;
            }
        },
        error: function (error) {
            console.log(error);
        },
    });
}
$('#edit_package_form').on('submit', function (e) {
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
                showToastMessage(result.message, "error");
                3;
                return;

            } else {
                showToastMessage(result.message, "success");
                setTimeout(function () {
                    window.location = base_url + '/admin/packages';
                }, 1000);

            }
        }
    });
});


// view packages js
$('.tenures').on('change', function () {
    var id = $(this).attr("data-package_id");
    var discount_value = $(this).find(":selected").attr("data-discount");
    var price = $(this).find(":selected").attr("data-price");
    var status;
    var icon;
    if (discount_value == '0') {
        status = "bg-danger";
        icon = " fa-times";
    } else {
        status = "bg-success";
        icon = " fa-check";
    };
    var myvar = '<div class="pricing-item  ">' +
        '<div class="pricing-item-icon ' + status + '"><i class="fa ' + icon + '"></i></div>' +
        '<div class="pricing-item-label">Discounted price' +
        '<span class="discount_price"> ' + discount_value + '</span>' +
        '</div>' +
        '</div>';

    $('#price' + id).empty(this);
    $('#price' + id).append(this.value);
    $("#discount_price" + id).children().last().remove()
    $('#discount_price' + id).append(myvar);
    if (discount_value == 0) {
        var price = $(this).find(":selected").attr("data-price");
        $('#price' + id).empty(price);
        $('#price' + id).append(price);
    } else {
        var discount = discount_value + ' <small class="discount-font">(<del>₹ ' + price + '</del>)</small>';
        $('#price' + id).empty(discount);
        $('#price' + id).append(discount);
    }

});
// subscription view JS......................

var start_date = "";
var end_date = "";
var subscription_type = "";
var date_filter_by = "";
$('#subscription_type').on('change', function () {
    subscription_type = $(this).find('option:selected').val();

});

$('#date_filter_by').on('change', function () {
    date_filter_by = $(this).find('option:selected').val();

});
$(function () {
    $('input[name="date_range"]').daterangepicker({
        opens: 'left'
    }, function (start, end, label) {
        start_date = start.format('YYYY-MM-DD');
        end_date = end.format('YYYY-MM-DD');
    });
});
$('#date_range').on('change', function () { });

function subscriptions_query(p) {
    return {
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        start_date: start_date,
        end_date: end_date,
        subscription_type: subscription_type,
        date_filter_by: date_filter_by,
    };
}
$('#filter').on('click', function (e) {
    $('#subscription_table').bootstrapTable('refresh');
});


let today_date;
$(document).ready(function () {
    var date = (new Date()).toISOString().split('T')[0];
    $('#starts_from').val(date);
    $('#reset').on('click', function (e) {
        e.preventDefault();
    });
    $('#user_identity').on('change', function () {
        var id = $(this).find('option:selected').val();
        var user_name = $(this).find(":selected").attr("data-fullname");
        $('#user_name').val(user_name);
    });
    $('#package_name').on('change', function (e) {
        var id = $(this).find(":selected").attr("data-package_id");
        var no_of_businessesme = $(this).find(":selected").attr("data-businesses");
        var no_of_delivery_boys = $(this).find(":selected").attr("data-delivery_boys");
        var no_of_products = $(this).find(":selected").attr("data-products");
        var no_of_customers = $(this).find(":selected").attr("data-customers");
        $('#no_of_businesses').val(no_of_businessesme);
        $('#no_of_delivery_boys').val(no_of_delivery_boys);
        $('#no_of_products').val(no_of_products);
        $('#no_of_customers').val(no_of_customers);
        $('#p_id').val(id);
        $.ajax({
            type: "get",
            url: site_url + '/admin/subscriptions/tenures/' + id,
            cache: false,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (result) {
                if (result.error == false) {
                    var options = "<option value=''>Select Tenure</option>";
                    var price = 0;
                    result.data.map((tenure) => {
                        price = (tenure.discounted_price < tenure.price && tenure.discounted_price > 0) ? tenure.discounted_price : tenure.price;
                        options += "<option data-tenure_name='" + tenure.tenure + "' data-tenure='" + tenure.months + "'  data-price='" + price + "' value=" + tenure.id + ">" + tenure.tenure + " </option>";
                    });
                } else {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message,
                        position: 'topRight'
                    });
                    var options = " <option value=''>No Tenures Found</option> ";
                }
                $('#package_tenure').html(options);
                $('#price').val("0.00");

            }
        });

    });
    $('#package_tenure').on('change', function (e) {
        var price = $(this).find(":selected").attr("data-price");
        var months = $(this).find(":selected").attr("data-tenure");
        var tenure_name = $(this).find(":selected").attr("data-tenure_name");
        $('#price').val(price);
        $('#months').val(months);
        $('#tenure_name').val(tenure_name);
        var start_date = document.getElementById("starts_from");
        var end_date = document.getElementById("ends_from");
        end_date_handler(start_date, end_date);
    });
    $('#starts_from').on('change', function () {
        var start_date = document.getElementById("starts_from");
        var end_date = document.getElementById("ends_from");
        end_date_handler(start_date, end_date);

    });


    function end_date_handler(start_date, end_date) {
        var currentDate = moment(start_date.value);
        var futureMonth = moment(currentDate).add($("#months").val(), "M");
        var futureMonthEnd = moment(futureMonth).endOf("month");

        if (currentDate.date() != futureMonth.date() && futureMonth.isSame(futureMonthEnd.format("YYYY-MM-DD"))) {
            futureMonth = futureMonth.add(1, "d");
        }
        $("#ends_from").val(futureMonth.format("YYYY-MM-DD"));
    }

    $('#add_subscription').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append(csrf_token, csrf_hash)
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
                    var message = "";
                    Object.keys(result.message).map((key) => {
                        iziToast.error({
                            title: 'Error!',
                            message: result.message[key],
                            position: 'topRight'
                        });
                    });

                } else {
                    $('#add_subscription_result').hide();
                    window.location = base_url + '/admin/subscriptions';
                }
            }
        });
    });
});
// create-vendor form 
$(document).ready(function () {
    $("#register_form").validate({
        rules: {
            first_name: {
                required: true,
            },
            last_name: {
                required: true,
            },
            email: {
                required: true,
            },
            identity: {
                required: true,
            },
            password: {
                required: true,
            },
            password_confirm: {
                required: true,
            },
        },
        messages: {
            first_name: {
                required: "First name can not be empty."
            },
            last_name: {
                required: "Last name can not be empty."
            },
            email: {
                required: "Email can not be empty."
            },
            identity: {
                required: "Identity can not be empty."
            },
            password: {
                required: "Password can not be empty."
            },
            password_confirm: {
                required: "Password confirm can not be empty."
            }
        },
        errorClass: 'text-danger'
    });
});

$('#register').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);
    $.ajax({
        type: "post",
        url: this.action,
        data: formData,
        beforeSend: function () {
            $('#register_btn').html('Please Wait..');
            $('#register_btn').attr('disabled', true);
        },
        cache: false,
        processData: false,
        contentType: false,
        dataType: 'json',

        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            if (result.error == true) {
                showToastMessage(result.message, "error");

            } else {
                showToastMessage(result.message, "success");
                window.location = base_url + '/login';

            }
        }
    });
})
// general-settings form
$('#general_setting_form').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash)
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
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message[key],
                        position: 'topRight'
                    });
                });
            } else {
                iziToast.success({
                    title: "Success!",
                    message: result.message,
                    position: "topRight",
                });
                // window.location = base_url + 'admin/home';
            }
        }
    });

});

$('#clear').on('click', function (e) {
    e.preventDefault();
    $('#title').val("");
    $('#support_email').val("");
    $('#logo').val("");
    $('#half_logo').val("");
    $('#favicon').val("");
    $('#currency_symbol').val("");
    $('#select_time_zone').val("");
    $('#phone').val("");
    $('#address').val("");
    $('#short_description').val("");
    $('#copyright_details').val("");
    $('#support_hours').val("");
});

// about us form setting
$('#about_us_setting_form').on('submit', function (e) {
    e.preventDefault();
    tinymce.triggerSave();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash)
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
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message[key],
                        position: 'topRight'
                    });
                });
            } else {
                window.location = base_url + '/admin/settings/about_us';
            }
        }
    });

});
$('#clear').on('click', function (e) {
    e.preventDefault();
    tinymce.activeEditor.setContent('');
});

//  Refund policy form settings
$('#refund_policy_setting_form').on('submit', function (e) {
    e.preventDefault();
    tinymce.triggerSave();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash)
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
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message[key],
                        position: 'topRight'
                    });
                });
            } else {
                window.location = base_url + '/admin/settings/refund_policy';
            }
        }
    });

});
$('#terms_and_conditions_setting_form').on('submit', function (e) {
    e.preventDefault();
    tinymce.triggerSave();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash)
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
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message[key],
                        position: 'topRight'
                    });
                });
            } else {
                window.location = base_url + '/admin/settings/terms_and_conditions';
            }
        }
    });

});
$('#privacy_policy_setting_form').on('submit', function (e) {
    e.preventDefault();
    tinymce.triggerSave();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash)
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
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message[key],
                        position: 'topRight'
                    });
                });
            } else {
                window.location = base_url + '/admin/settings/privacy_policy';
            }
        }
    });

});


$('#clear').on('click', function (e) {
    e.preventDefault();
    tinymce.activeEditor.setContent('');

});
// payment gateway form
$('#payment_gateway_setting_form').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash)
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
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message[key],
                        position: 'topRight'
                    });
                });
            } else {
                window.location = base_url + '/admin/settings/payment_gateway';
            }
        }
    });

});
// SMTP Email settings
$('#email_settings').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash)
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
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message[key],
                        position: 'topRight'
                    });
                });
            } else {
                window.location = base_url + '/admin/settings/email';
            }
        }
    });

});

// units form
function select_parent_id() {
    var unit = $('#unit').val();
}
$(document).ready(function () {
    $('#unit').on('change', function () {
        select_parent_id();
    });
    $('#units_form').on('submit', function (e) {
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
                    var message = "";
                    Object.keys(result.message).map((key) => {
                        showToastMessage(result.message[key], "error");
                        3;
                        return;
                    });
                } else {
                    window.location = base_url + '/admin/units';
                    showToastMessage(result.message, "success");
                    3;
                    return
                }
            }
        });

    });
});

$('#admin_units_table').on('check.bs.table', function (e, row) {
    e.preventDefault();
    $('#name').val(row.name);
    $('#unit_id').val(row.id);
    $('#symbol').val(row.symbol);
    $('#parent_id').val(row.parent_id);
    $('#conversion').val(row.conversion);
});


$("#admin_profile_form").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash)
    var check = false;
    $.ajax({
        type: "POST",
        url: $(this).attr("action"),
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            if (!result["error"]) {
                var data = result["data"];
                if (data['old'] != "" && data['new'] != "") {
                    check = true;
                }
                $("#header_name").html(data["first_name"]);
                $("#f_name").html(data["first_name"]);
                $("#l_name").html(data["last_name"]);
                showToastMessage(result["message"], "success");
            } else {
                Object.keys(result.message).map((key) => {
                    showToastMessage(result["message"][key], "error");
                });
                return;
            }
            setTimeout(() => {
                if (check) {
                    window.location.href = base_url + "/auth";
                } else {
                    location.reload();
                }
            }, 1000);
        },
    });
});
// manage profile
$("#admin_profile_form").validate({
    rules: {
        first_name: {
            required: true,
        },
        last_name: {
            required: true,
        },
        email: {
            required: true,
        },
        identity: {
            required: true,
        }

    },
    messages: {
        first_name: {
            required: "First name can not be empty."
        },
        last_name: {
            required: "Last name can not be empty."
        },
        email: {
            required: "Email can not be empty."
        },
        identity: {
            required: "Mobile can not be empty."
        }

    },
    errorClass: 'text-danger'
});
// categories form
$('#admin_categories_table').on('check.bs.table', function (e, row) {
    e.preventDefault();
    $('#name').val(row.name);
    $('#category_id').val(row.id);
    $('#parent_id').val(row.parent_id);
    if (row.active == 1) {
        $('#status').attr("checked", true);
    } else {
        $('#status').attr("checked", false);
    }
});
$('#categories_form').on('submit', function (e) {
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
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message[key],
                        position: 'topRight'
                    });
                });
            } else {
                location.reload();
                $("#categories_form").reset();
                showToastMessage(result.message, "success")
            }
        }
    });

});
// Tax form
$('#tax_form').on('submit', function (e) {
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
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message[key],
                        position: 'topRight'
                    });
                });
            } else {
                window.location = base_url + '/admin/tax';
            }
        }
    });

});
// transactions js


let txn_start_date = "";
let txn_end_date = "";
let transaction_status = "";
let txn_provider = "";

function transaction_params(p) {
    return {
        user_id: userId,
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        start_date: txn_start_date,
        end_date: txn_end_date,
        txn_provider: txn_provider,
        transaction_status: transaction_status,
    };
}
$("#payment_method").on("change", function () {
    txn_provider = $(this).val();
});
$("#transaction_status").on("change", function () {
    transaction_status = $(this).val();
});
$('#transaction_filter_btn').on('click', function (e) {
    $('#admin_transactions_table').bootstrapTable('refresh');
});

function refresh_table(id) {
    $('#' + id).bootstrapTable('refresh');
}

//  dashboard chart 
if ($("#myChart").length > 0) {
    var total_sale = [];
    var month_name;
    var data = [];

    $.ajax({
        type: "get",
        url: site_url + '/admin/home/fetch_sales',
        cache: false,
        dataType: 'json',
        success: function (result) {
            total_sale = result.total_sale
            month_name = result.month_name
            var data = {
                labels: month_name,
                datasets: [{
                    label: 'sale',
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

if ($("#pieChart").length > 0) {
    $.ajax({
        type: "get",
        url: site_url + '/admin/home/fetch_data',
        cache: false,
        dataType: 'json',
        success: function (result) {
            const data = {
                labels: [
                    'vendors',
                    'packages',
                    'No. of transactions'
                ],
                datasets: [{
                    label: 'sale',
                    data: [result.vendors, result.sold_packages, result.earnings],
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)'
                    ],
                    hoverOffset: 4
                }]
            };

            const config = {
                type: 'doughnut',
                data: data,
            };

            const myChart = new Chart(
                document.getElementById('pieChart'),
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
if (document.getElementById('system-update-dropzone')) {

    var systemDropzone = new Dropzone("#system-update-dropzone", {
        url: base_url + '/admin/updater/upload_update_file',
        paramName: "update_file",
        autoProcessQueue: false,
        parallelUploads: 1,
        maxFiles: 1,
        timeout: 360000,
        autoDiscover: false,
        addRemoveLinks: true,
        dictRemoveFile: 'x',
        dictMaxFilesExceeded: 'Only 1 file can be uploaded at a time ',
        dictResponseError: 'Error',
        uploadMultiple: true,
        dictDefaultMessage: '<p><input type="button" value="Select Files" class="btn btn-success" /><br> or <br> Drag & Drop System Update / Installable / Plugin\'s .zip file Here</p>',
    });

    systemDropzone.on("addedfile", function (file) {
        var i = 0;
        if (this.files.length) {
            var _i, _len;
            for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) {
                if (this.files[_i].name === file.name && this.files[_i].size === file.size && this.files[_i].lastModifiedDate.toString() === file.lastModifiedDate.toString()) {
                    this.removeFile(file);
                    i++;
                }
            }
        }
    });

    systemDropzone.on("error", function (file, response) {
        console.log(response);
    });

    systemDropzone.on('sending', function (file, xhr, formData) {
        formData.append(csrf_token, csrf_hash);
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.response);
                csrf_token = response.csrf_token;
                csrf_hash = response.csrf_hash;
                if (response['error'] == false) {
                    showToastMessage(response['message'], "success");
                } else {
                    showToastMessage(response['message'], "error");

                }
                $(file.previewElement).find('.dz-error-message').text(response.message);
            }
        };
    });
    $('#system_update_btn').on('click', function (e) {
        e.preventDefault();
        systemDropzone.processQueue();
    });
}