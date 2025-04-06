"use strict";

$(document).on('show.bs.modal', '#system_pages', function (event) {
    $(".content").html("")
    $(".modal-title").html("")
    var triggerElement = $(event.relatedTarget);
    var current_selected_variant = triggerElement;
    var name = $(current_selected_variant).data('name');
    var system_page;
    if (name == "about_us") {
        system_page = site_url + '/system_pages/about_us'
    }
    if (name == "privacy_policy") {
        system_page = site_url + '/system_pages/privacy_policy'
    }
    if (name == "refund_policy") {
        system_page = site_url + '/system_pages/refund_policy'
    }
    if (name == "terms_and_conditions") {
        system_page = site_url + '/system_pages/terms_and_conditions'
    }
    $.ajax({
        type: "post",
        url: system_page,
        data: {
            [csrf_token]: csrf_hash
        },
        dataType: 'json',
        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            $(".content").append(result.text)
            $(".modal-title").append(result.header)
        }
    });
});



function set_vendor() {
    $("#identity").val("9876543210");
    $("#password").val("12345678");
}

function set_delivery_boy() {
    $("#identity").val("9988776655");
    $("#password").val("12345678");
}
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
            console.log("dhsfka;dfkladl;fj;ksj");
            
            if (result.error == true) {
                Object.keys(result.message).map((key) => {
                    showToastMessage(result.message[key], "error");
                });
            } else {

                if (result.vendor == true) {
                    showToastMessage(result.message, "success")
                    console.log("here");
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

$('#forgot_password').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);

    $.ajax({
        type: 'POST',
        url: this.action,
        data: formData,
        beforeSend: function () {
            $('#contact_submit').html('Please Wait..');
            $('#contact_submit').attr('disabled', true);
        },
        cache: false,
        processData: false,
        contentType: false,
        dataType: 'json',

        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            if (result['error'] == false) {

                $('#forgot_password_form').hide();
                $('#success-result').html(result['message']);
                $('#success-result').removeClass("d-none");
            } else {
                $('#forgot_password_form').show();
                $('#login-result').html(result['message']);
                $('#login-result').show().delay(6000).fadeOut();
                $('#login-result').removeClass("d-none").delay(6000).queue(function () {
                    $(this).addClass("d-none").dequeue();
                });
                $('#loginbtn').val('Forgot Password');
                $('#loginbtn').attr('disabled', false);
            }

        }
    });
});
