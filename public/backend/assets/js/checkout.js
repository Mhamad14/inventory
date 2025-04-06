"use strict";
let pre_payment_error = false;
let stripe1;
let stripe_flag = false;
$("#stripe_div").slideUp();
var user_id = $("#user_id").val();


function razorpay_script(
    razorpay_key,
    amount,
    company,
    razorpay_order_id,
    username,
    email,
    logo,
    phone,
    description = "Product Purchase",
    currency = $("#razorpay_currency").val()
) {

    var load_script = function (path) {
        var result = $.Deferred(),
            script = document.createElement("script");

        script.async = "async";
        script.type = "text/javascript";
        script.src = path;
        script.onload = script.onreadystatechange = function (_, isAbort) {
            if (!script.readyState || /loaded|complete/.test(script.readyState)) {
                if (isAbort) result.reject();
                else result.resolve();
            }
        };
        script.onerror = function () {
            result.reject();
        };
        $("head")[0].appendChild(script);
        return result.promise();
    };

    load_script("https://checkout.razorpay.com/v1/checkout.js").then(function () {
        var options = {
            key: razorpay_key, // Enter the Key ID generated from the Dashboard
            amount: amount * 100, // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
            currency: currency,
            name: company,
            description: description,
            image: logo,
            order_id: razorpay_order_id, //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
            handler: function (response) {

                $("#razorpay_payment_id").val(response.razorpay_payment_id);
                $("#razorpay_signature").val(response.razorpay_signature);
                $(".modal").modal("show");
                post_payment("razorpay").done(function (result) {
                    if (result.error == false) {
                        setTimeout(function () {
                            $(".modal").modal("hide");
                        }, 500);
                    } else {
                        $(".modal").modal("show");
                        setTimeout(function () {
                            $(".modal").modal("hide");
                        }, 500);
                    }
                });
            },
            prefill: {
                name: username,
                email: email,
                contact: phone,
            },
            notes: {
                address: username + "Purchase",
            },
            theme: {
                color: "#3399cc",
            },
            escape: false,
            modal: {
                ondismiss: function () {
                    $("#buy_package").attr("disabled", false).html("Buy Now");
                },
            },
        };
        window.rzpay = new Razorpay(options);
        rzpay.open();
    });
}


// Add an event listener for when the user clicks the submit button to pay

function makePayment() {

    var public_key = $("#flutterwave_public_key").val();
    var tx_ref = '' + Math.floor((Math.random() * 1000000000) + 1); //Generate a random id for the transaction reference
    var amount = $("#price").val();
    var payment_method = "Flutterwave";
    var currency = $("#flutterwave_currency_symbol").val();
    var company = $("#app_name").val();
    var logo = $("#logo").val();
    var email = $("#email").val();
    var name = $("#vendor_name").val();
    var phone = $("#phone").val();

    const request_body = {
        [csrf_token]: csrf_hash,
        tx_ref: tx_ref,
        public_key: public_key,
        payment_method: payment_method,
        amount: amount,
        user_id: user_id,
        currency: currency,
    }
    $.ajax({
        type: "post",
        url: base_url + "/vendor/payments/pre_payment_setup",
        data: request_body,
        dataType: 'json',
        success: function (result) {
            csrf_token = result.csrf_token;
            csrf_hash = result.csrf_hash;
            if (result.error == false) {
                amount = result.amount;
                phone = phone;
                email = email;
                name = name;
                company = company;
                var d = new Date();
                var ms = d.getMilliseconds();
                var number = Math.floor(1000 + Math.random() * 9000);
                tx_ref = company + '-' + ms + '-' + number;
                public_key = public_key;
                FlutterwaveCheckout({
                    public_key: public_key,
                    tx_ref: tx_ref,
                    amount: amount,
                    currency: currency,
                    payment_options: "card,mobilemoney,ussd",
                    customer: {
                        email: email,
                        phone_number: phone,
                        name: name,
                    },
                    callback: function (result) { // specified callback function
                        if (result.status == "successful") {
                            $("#flutterwave_transaction_id").val(result.transaction_id);
                            $("#flutterwave_transaction_ref").val(result.tx_ref);
                            post_payment("flutterwave");

                        } else {
                            location.href = base_url + "/vendor/payments/payment_failed";
                        }
                    },
                    customizations: {
                        company: company,
                        description: "Payment for product purchase",
                        logo: logo,
                    },
                });
            } else {
                return showToastMessage(result["message"], "error");
            }
        },
    })


}

$("#buy_package").on("click", function (e) {
    e.preventDefault();
    $("#stripe_div").slideUp();
    if (document.getElementById("razorpay").checked == true) {
        $("#buy_package").attr("disabled", true).html("Please wait");
        var payment_method = "Razorpay";
        var amount = $("#price").val();
        const request_body = {
            [csrf_token]: csrf_hash,
            payment_method: payment_method,
            amount: amount,
            user_id: user_id,
        }

        $.ajax({
            type: "post",
            url: base_url + "/vendor/payments/pre_payment_setup",
            data: request_body,
            dataType: 'json',
            success: function (data) {
                csrf_token = data.csrf_token;
                csrf_hash = data.csrf_hash;
                if (data.error == false) {
                    $("#razorpay_order_id").val(data.order_id);
                } else {
                    pre_payment_error = true;

                    return showToastMessage(data["message"], "error");
                }
            },
        }).then(() => {
            if (!pre_payment_error) {
                var razorpay_key = $("#razorpay_key_id").val();
                var amount = $("#price").val();
                var company = $("#app_name").val();
                var razorpay_order_id = $("#razorpay_order_id").val();
                var username = "subscription";
                var email = "test@test.com";
                var logo = $("#logo").val();
                var phone = "9876543210";
                razorpay_script(
                    razorpay_key,
                    amount,
                    company,
                    razorpay_order_id,
                    username,
                    email,
                    logo,
                    phone
                );
            } else {
                $("#buy_package").attr("disabled", false).html("Buy Now");
            }
        });
    }

    if (document.getElementById("flutterwave").checked == true) {
        $("#buy_package").attr("disabled", true).html("Please wait");
        e.preventDefault();
        makePayment();
    }
    if (document.getElementById("stripe").checked == true) {
        $("#buy_package").attr("disabled", true).html("Please wait");
        var amount = $("#price").val();
        $.post(
            base_url + "/vendor/payments/pre_payment_setup", {
                [csrf_token]: csrf_hash,
                payment_method: "stripe",
                amount: amount,
                user_id: user_id,
                plan_id: $("#plan_id").val(),
                tenure: $("#tenure_id").val(),
            },
            function (data) {
                csrf_token = data.csrf_token;
                csrf_hash = data.csrf_hash;
                if (data.error) {
                    pre_payment_error = true;
                    showToastMessage(data.message, "error");
                } else {
                    console.log(data);
                    $("#stripe_client_secret").val(data.client_secret);
                    $("#stripe_payment_id").val(data.id);
                    var stripe_client_secret = data.client_secret;
                    stripe_payment(stripe1.stripe, stripe1.card, stripe_client_secret);
                }
            },
            "json"
        ).then(() => {
            $("#buy_package").attr("disabled", false).html("Buy Now");
        });
    }

});
// stripe payments
$("input[name='payment_type']").on("change", function (e) {
    var payment_method = $("input[name=payment_type]:checked").val();
    if (payment_method == "stripe") {
        $("#stripe_div").slideDown();
        stripe1 = stripe_setup($("#stripe_key").val());
    } else {
        $("#stripe_div").slideUp();
    }
});

function stripe_payment(stripe, card, clientSecret) {
    // Calls stripe.confirmCardPayment
    // If the card requires authentication Stripe shows a pop-up modal to
    // prompt the user to enter authentication details without leaving your page.
    stripe
        .confirmCardPayment(clientSecret, {
            payment_method: {
                card: card,
            },
        })
        .then(function (result) {
            $("#subscribe").attr("disabled", false).html("Buy Now");
            if (result.error) {
                // Show error to your customer
                var errorMsg = document.querySelector("#card-error");
                errorMsg.textContent = result.error.message;
                setTimeout(function () {
                    errorMsg.textContent = "";
                }, 4000);

                console.log(error);

            } else {
                // The payment succeeded!
                showToastMessage(result.paymentIntent.status, "success");
                setTimeout(function () {
                    location.href = base_url + "/vendor/payments/payment_success";
                }, 1000);
            }
        });
}

function stripe_setup(key) {
    // A reference to Stripe.js initialized with a fake API key.
    // Sign in to see examples pre-filled with your key.
    var stripe = Stripe(key);
    // Disable the button until we have Stripe set up on the page
    var elements = stripe.elements();
    var style = {
        base: {
            color: "#32325d",
            fontFamily: "Arial, sans-serif",
            fontSmoothing: "antialiased",
            fontSize: "16px",
            "::placeholder": {
                color: "#32325d",
            },
        },
        invalid: {
            fontFamily: "Arial, sans-serif",
            color: "#fa755a",
            iconColor: "#fa755a",
        },
    };

    var card = elements.create("card", {
        style: style,
    });
    card.mount("#stripe-card");

    card.on("change", function (event) {
        // Disable the Pay button if there are no card details in the Element
        document.querySelector("button").disabled = event.empty;
        document.querySelector("#card-error").textContent = event.error ?
            event.error.message :
            "";
    });
    return {
        stripe: stripe,
        card: card,
    };
}

function post_payment(provider) {
    let req_body;
    if (provider == "razorpay") {
        req_body = {
            [csrf_token]: csrf_hash,
            plan_id: $("#plan_id").val(),
            tenure_id: $("#tenure_id").val(),
            txn_id: $("#razorpay_payment_id").val(),
            provider: provider,
        };
    }
    if (provider == "flutterwave") {
        req_body = {
            [csrf_token]: csrf_hash,
            plan_id: $("#plan_id").val(),
            tenure_id: $("#tenure_id").val(),
            txn_id: $("#flutterwave_transaction_id").val(),
            txn_ref: $("#flutterwave_transaction_ref").val(),
            provider: provider,
        };
    }
    return $.ajax({
        type: "POST",
        data: req_body,
        url: base_url + "/vendor/payments/post_payment",
        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            var data = result["data"];
            if (data.error == true) {
                showToastMessage(result["message"], "error");
                location.href = base_url + "/vendor/payments/payment_failed";

                
            } else {
                showToastMessage(result["message"], "success");
                location.href = base_url + "/vendor/payments/payment_success";
            }
        },
    });
}