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
      $(".navbar li a").removeClass("active");
      $(this).closest("li").addClass("active");
      if ($(this).parents().hasClass("dropdown-menu")) {
        $(this).parents().addClass("active");
        $(this).parents().show();
      }
    }
  });
});
function update_status_list() {
  $.ajax({
    type: "POST",
    url: "get_status_list",
    cache: false,
    processData: false,
    contentType: false,
    success: function (response) {
      let result = JSON.parse(response);

      if (result.is_error == false) {
        console.log(result.data);

        populateSelectList("status", result.data);
      }
    },
  });
}
function populateSelectList(selectId, optionsArray) {
  const selectElement = document.getElementById(selectId);

  if (!selectElement) {
    console.error(`Select element with ID "${selectId}" not found.`);
    return;
  }

  // Clear existing options (optional, if needed)
  selectElement.innerHTML = '<option value="">Select status</option>';
  // Add new options
  optionsArray.forEach((option) => {
    const optionElement = document.createElement("option");
    optionElement.value = option.id;
    optionElement.textContent = option.status;

    selectElement.appendChild(optionElement);
  });
}
function resetForm(form) {
  // clearing inputs
  var inputs = form.getElementsByTagName("input");
  for (var i = 0; i < inputs.length; i++) {
    switch (inputs[i].type) {
      // case 'hidden':
      case "text":
        inputs[i].value = "";
        break;
      case "radio":
      case "checkbox":
        inputs[i].checked = false;
      case "email":
        inputs[i].value = "";
        break;
      case "number":
        inputs[i].value = "";
        break;
    }
  }

  // clearing selects
  var selects = form.getElementsByTagName("select");
  for (var i = 0; i < selects.length; i++) selects[i].selectedIndex = 0;

  // clearing textarea
  var text = form.getElementsByTagName("textarea");
  for (var i = 0; i < text.length; i++) text[i].innerHTML = "";

  return false;
}
$("#update_profile_form").on("submit", function (e) {
  e.preventDefault();
  var formData = new FormData(this);
  formData.append(csrf_token, csrf_hash);
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
      csrf_token = result["csrf_token"];
      csrf_hash = result["csrf_hash"];
      if (!result["error"]) {
        var data = result["data"];
        if (data["old"] != "" && data["new"] != "") {
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
//  businesses form/table
function edit_business(e) {
  $("#image_edit").html("");
  var business_id = $(e).data("business_id");
  $.ajax({
    type: "get",
    url: site_url + "admin/businesses/edit_business?id=" + business_id,
    cache: false,
    dataType: "json",
    success: function (result) {
      if (result.error == false) {
        var img =
          '<div class="img-fluid"><img class="icon-box" src="' +
          base_url +
          "/" +
          result.business.icon +
          '" alt=""></div>';
        $('input[name="name"]').val(result.business.name);
        $('input[name="business_id"]').val(result.business.id);
        $('input[name="old_icon"]').val(result.business.icon);
        $('textarea[name="description"]').val(result.business.description);
        $('textarea[name="address"]').val(result.business.address);
        $('input[name="contact"]').val(result.business.contact);
        $('input[name="tax_name"]').val(result.business.tax_name);
        $('input[name="tax_value"]').val(result.business.tax_value);
        $('textarea[name="bank_details"]').val(result.business.bank_details);
        $('input[name="email"]').val(result.business.email);
        $('input[name="website"]').val(result.business.website);
        let status = result.business.status == 1 ? true : false;
        $('input[name="status"]').attr("checked", status);
        $("#image_edit").append(img);
        $(document).scrollTop(0, 0, 500);
      } else {
        iziToast.error({
          title: "Error!",
          message: result.message,
          position: "topRight",
        });
      }
    },
  });
}
$("#business_form").on("submit", function (e) {
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
          showToastMessage(result["message"][key], "error");
        });
      } else {
        window.location = base_url + "/admin/businesses";
        showToastMessage(result["message"], "success");
      }
    },
  });
});
$("#supplier_form").on("submit", function (e) {
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
          showToastMessage(result["message"][key], "error");
        });
      } else {
        window.location = base_url + "/admin/suppliers";
        showToastMessage(result["message"], "success");
      }
    },
  });
});
// vendor units form........................
function select_parent_id() {
  var unit = $("#unit").val();
}
$(document).ready(function () {
  $("#unit").on("change", function () {
    select_parent_id();
  });
  $("#vendor_units_form").on("submit", function (e) {
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
          window.location = base_url + "/admin/units";
        }
      },
    });
  });
});
// vendor categories form
$("#vendor_categories_form").on("submit", function (e) {
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
        window.location = base_url + "/admin/categories";
      }
    },
  });
});
//  Add product button check if business of vendor added first
$(document).ready(function () {
  $("#add_product_btn").on("click", function (e) {
    e.preventDefault();
    var business_id = $("#business_id").val();
    if (business_id == "0" || business_id == "") {
      if (!confirm("Please Add/Select your BUSINESS first!")) {
        return false;
      }
    } else {
      window.location = base_url + "/admin/products/add_products";
    }
  });
});
// variants_modal
$(document).on("show.bs.modal", "#variants_Modal", function (event) {
  var triggerElement = $(event.relatedTarget);
  var current_selected_variant = triggerElement;
  var id = $(current_selected_variant).data("id");

  var existing_url = $(this).find("#variants_table").data("url");
  if (existing_url.indexOf("?") > -1) {
    var temp = $(existing_url).text().split("?");
    var new_url = temp[0] + "?product_id=" + id;
  } else {
    var new_url = existing_url + "?product_id=" + id;
  }
  $("#variants_table").bootstrapTable("refreshOptions", {
    url: new_url,
  });
});
// Product form
var product_type;

function toggle_stock_management() {
  var stock_management = $("#stock_management").is(":checked");
  if (stock_management) {
    $("#stock_management_type_div").show();

    /* 1 - product_level | 2 - varaint_level  */
    var stock_management_type = $("#stock_management_type").val();
    if (stock_management_type == 1) {
      $(".stock_product_level").show();
      $(".stock_variant_level").hide();
    } else if (stock_management_type == 2) {
      $(".stock_product_level").hide();
      $(".stock_variant_level").show();
    } else {
      $(".stock_product_level").hide();
      $(".stock_variant_level").hide();
    }
  } else {
    $("#stock_management_type_div").hide();
    $(".stock_variant_level").hide();
    $(".stock_product_level").hide();
  }
}

function toggle_product_type() {
  var product_type = $("#product_type").val();
  if (product_type == "simple") {
    $(".add_btn_action").hide();
    $("#variant").empty();
  } else {
    $(".add_btn_action").show();
  }
}

$(document).ready(function () {
  toggle_stock_management();
  $(".add_btn_action").hide();

  $("#product_type").on("change", function () {
    toggle_product_type();
  });

  $("#stock_management_type").on("change", function () {
    toggle_stock_management();
  });

  $("#stock_management").on("change", function () {
    toggle_stock_management();
  });
  toggle_product_type();

  var i = 0;
  var j = 0;
  var k = 0;
  $("#add_variant").on("click", function (e) {
    e.preventDefault();
    var units = $("#units").val();
    if (units) {
      units = JSON.parse(units);
      var options = "<option value=''>Select Unit</option>";
      $.each(units, function (i, units) {
        options +=
          '<option value = "' +
          units["id"] +
          '" > ' +
          units["name"] +
          "</option>";
      });

      var all_warehouses = $("#all_warehouses").val();
      if (all_warehouses) {
        all_warehouses = JSON.parse(all_warehouses);
        var warehouse_options = "<option value=''>Select Warehouse</option>";
        $.each(all_warehouses, function (i, warehouse) {
          warehouse_options +=
            '<option value = "' +
            warehouse["id"] +
            '" > ' +
            warehouse["name"] +
            "</option>";
        });

        var html = `
            <div class="variant-item py-1 mb-3 border-top border-2">
                <div class="d-flex justify-content-between my-1">
                    <div>
                        <p class="text-black font-weight-bolder">Variant ${
                          $(".variant-item").length + 1
                        }</p>
                    </div>
                    <div class="d-flex gap-3">
                        <div>
                            <button class="btn btn-icon btn-danger  remove_variant" 
                                    data-variant_id=""
                                    name="remove_variant"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Remove variant">
                                <i class="fas fa-trash"></i>
                                <span class="d-none d-md-inline">Remove variant</span>
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-primary addWarehouseBtn" 
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Add warehouse"
                                    data-variant_index="${
                                      $(".variant-item").length
                                    }"
                                    type="button">
                                <i class="fas fa-plus"></i>
                                <span class="d-none d-md-inline">Add warehouse</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-2 custom-col">
                        <label>Variant Name<span class="asterisk text-danger"> *</span></label>
                        <input type="text" class="form-control" id="variant_name" name="variant_name[]" placeholder="Ex. 1 kg..">
                    </div>
                    <div class="col-md-2 custom-col">
                        <label id=""> Variant Barcode </label>
                        <input type="text" class="form-control" id="variant_barcodee" name="variant_barcode[]"  placeholder="Enter Barcode , Ex : 9875855">
                    </div>
                    <div class="col-md-2 custom-col">
                        <label>Sale Price (₹)<span class="asterisk text-danger"> *</span></label>
                        <input type="number" class="form-control" id="sale_price" name="sale_price[]" min="0.00" placeholder="0.00">
                    </div>
                    <div class="col-md-2 custom-col">
                        <label>Purchase Price (₹)<span class="asterisk text-danger"> *</span></label>
                        <input type="number" class="form-control" id="purchase_price" name="purchase_price[]" min="0.00" placeholder="0.00">
                    </div>
                    <div class="col-md-2 custom-col stock_variant_level">
                        <label>Unit<span class="asterisk text-danger"> *</span></label>
                        <select class="form-control" id="unit_id" name="unit_id[]">
                            ${options}
                        </select>
                    </div>
                    <div class="col-md-2 custom-col stock_variant_level">
                        <label>Stock<span class="asterisk text-danger"> *</span></label>
                        <input type="number" class="form-control" id="stock" step="0.1" min="0.1" name="stock[]" min="0.00" placeholder="0.00">
                    </div>
                    <div class="col-md-2 custom-col stock_variant_level">
                        <label>Minimum Stock<span class="asterisk text-danger"> *</span></label>
                        <input type="number" class="form-control" id="qty_alert" step="0.1" min="0.1" name="qty_alert[]" min="0.00" placeholder="0.00">
                    </div>
                </div>
                
                <div class="warehouses">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="warehouse_id">Warehouse</label><span class="asterisk text-danger">*</span>
                            <select class="form-control" id="warehouse_id" name="warehouses[${
                              $(".variant-item").length
                            }][warehouse_ids][]">
                                ${warehouse_options}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="warehouse_stock">Warehouse Stock</label><span class="asterisk text-danger">*</span>
                            <input type="number" class="form-control No-negative" id="warehouse_stock" step="0.1" min="0.1" name="warehouses[${
                              $(".variant-item").length
                            }][warehouse_stock][]">
                        </div>
                        <div class="col-md-3">
                            <label for="warehouse_qty_alert">Warehouse Minimum Stock Level</label><span class="asterisk text-danger">*</span>
                            <input type="number" class="form-control No-negative" id="warehouse_qty_alert" step="0.1"  min="0.1" name="warehouses[${
                              $(".variant-item").length
                            }][warehouse_qty_alert][]">
                        </div>
                    </div>
                </div>
            </div>`;

        $("#variant").append(html);
        toggle_stock_management();
      }
    }
  });
  $(document).on("click", ".remove_variant", function (e) {
    e.preventDefault();
    $(this).parent().parent().parent().parent().remove();
  });
  $("#team_members_formss").on("submit", function (e) {
    e.preventDefault();
    let isValid = 1;
    if ($("#password_confirm").val() != $("#password").val()) {
      isValid = 0;
    }

    if (!isValid) {
      iziToast.error({
        title: "Error!",
        message: "Confirm password is not same as password",
        position: "topRight",
      });
      return;
    }

    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);

    $.ajax({
      type: "post",
      url: this.action,
      data: formData,
      cache: false,
      processData: false,
      contentType: false,
      // dataType: "json",
      success: function (result) {
        csrf_token = result["csrf_token"];
        csrf_hash = result["csrf_hash"];
        var message = result.message;

        if (result.error == true) {
          Object.keys(result.message).map((key) => {
            showToastMessage(result["message"][key], "error");
          });
        } else {
          showToastMessage(message, "success");
          setTimeout(function () {
            window.location = base_url + "/admin/team_members";
          }, 2000);
        }
      },
    });
  });
  $("#team_members_form").on("submit", function (e) {
    e.preventDefault();
    let isValid = 1;
    if ($("#password_confirm").val() != $("#password").val()) {
      isValid = 0;
    }

    if (!isValid) {
      iziToast.error({
        title: "Error!",
        message: "Confirm password is not same as password",
        position: "topRight",
      });
      return;
    }

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
            showToastMessage(result["message"][key], "error");
          });
        } else {
          showToastMessage(result["message"], "success");
          setTimeout(function () {
            window.location = base_url + "/admin/team_members";
          }, 2000);
        }
      },
    });
  });
  // remove-variant
  $(document).on("click", ".remove_variant", function (e) {
    e.preventDefault();
    if (!confirm("Are you sure want to delete?")) {
      return false;
    }
    e.stopPropagation();
    e.stopImmediatePropagation();
    var variant_id = $(this).attr("data-variant_id");
    $.ajax({
      type: "get",
      url: site_url + "/admin/products/remove_variant/" + variant_id,
      cache: false,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (result) {
        if (result.error == false) {
          iziToast.success({
            title: "Success!",
            message: result.message,
            position: "topRight",
          });
        } else {
          iziToast.error({
            title: "Error!",
            message: result.message,
            position: "topRight",
          });
        }
      },
    });
    $(this).parent().parent().parent().remove();
  });

  $("#reset").on("click", function (e) {
    e.preventDefault();
  });
  $("#product_form").on("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    var stock_management_type = $("#stock_management_type").val();

    if (stock_management_type == 1) {
      // Get all the stock input elements
      const stockInput = document.querySelector(
        'input[name="simple_product_stock"]'
      );
      let index = 0;

      const stockValue = parseFloat(stockInput.value) || 0;

      // Get corresponding warehouse stock elements for this stock
      const warehouseStocks = document.querySelectorAll(
        `input[name="warehouses[${index}][warehouse_stock][]"]`
      );

      let totalWarehouseStock = 0;

      // Sum all warehouse stock values
      warehouseStocks.forEach(function (warehouseStockInput) {
        totalWarehouseStock += parseFloat(warehouseStockInput.value) || 0;
      });

      // Compare the total warehouse stock to the stock value
      if (totalWarehouseStock !== stockValue) {
        isValid = false;

        iziToast.error({
          title: "Error! Mismatch Stock",
          message: `Total of all warehouse stocks must be equal to variant stock (for variant ${
            index + 1
          })`,
          position: "topRight",
        });
      }
      index++;
    } else if (stock_management_type == 2) {
      // Get all the stock input elements
      const stockInputs = document.querySelectorAll('input[name="stock[]"]');
      let index = 0;
      // Loop through each stock input
      stockInputs.forEach(function (stockInput) {
        const stockValue = parseFloat(stockInput.value) || 0;

        // Get corresponding warehouse stock elements for this stock
        const warehouseStocks = document.querySelectorAll(
          `input[name="warehouses[${index}][warehouse_stock][]"]`
        );
        let totalWarehouseStock = 0;

        // Sum all warehouse stock values
        warehouseStocks.forEach(function (warehouseStockInput) {
          totalWarehouseStock += parseFloat(warehouseStockInput.value) || 0;
        });

        // Compare the total warehouse stock to the stock value
        if (totalWarehouseStock !== stockValue) {
          isValid = false;
          // alert(`Total of all warehouse stocks must be equal to variant stock (for variant ${index + 1})`);
          iziToast.error({
            title: "Error! Mismatch Stock",
            message: `Total of all warehouse stocks must be equal to variant stock (for variant ${
              index + 1
            })`,
            position: "topRight",
          });
        }
        index++;
      });
    }

    if (isValid) {
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
          console.log(result);
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
            window.location = base_url + "/admin/products";
            showToastMessage(result.message, "success");
          }
        },
      });
    }
  });
});
//  variant table update status
function update_status(element) {
  if (!confirm("Are you sure want to update status?")) {
    return false;
  }
  var status;
  var id;
  if (!$(element).is(":checked")) {
    id = $(element).attr("data-id");
    status = "0";
  } else {
    var id = $(element).attr("data-id");
    status = "1";
  }
  $.ajax({
    type: "get",
    url:
      site_url +
      "/admin/products/update_variant_status?id=" +
      id +
      "&status=" +
      status,
    cache: false,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (result) {
      if (result.error == false) {
        $("table").bootstrapTable("refresh");
      } else {
        iziToast.error({
          title: "Error!",
          message: result.message,
          position: "topRight",
        });
      }
    },
  });
}
// tooltip
var tooltipTriggerList = [].slice.call(
  document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl);
});
// update default business
function update_default_business(element) {
  if (!confirm("Are you sure want to change default business?")) {
    return false;
  }

  var id = $(element).attr("data-id");
  var default_business = $(element).is(":checked") ? "1" : "0";

  $.ajax({
    type: "get",
    url:
      site_url +
      "/admin/businesses/update_default_business? id=" +
      id +
      "&default_business=" +
      default_business,
    cache: false,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (result) {
      if (result.error == false) {
        $("table").bootstrapTable("refresh");
        iziToast.success({
          title: "Success!",
          message: result.message,
          position: "topRight",
        });
        setTimeout(() => {
          window.location = base_url + "/admin/home";
        }, 2000);
      } else {
        iziToast.error({
          title: "Error!",
          message: result.message,
          position: "topRight",
        });
      }
    },
  });
}
$(document).ready(function () {
  $("#add_service_btn").on("click", function (e) {
    e.preventDefault();
    var business_id = $("#business_id").val();
    if (business_id == "0" || business_id == "") {
      if (!confirm("Please Add/Select your BUSINESS first!")) {
        return false;
      }
    } else {
      window.location = base_url + "/admin/services/add_service";
    }
  });
});
// Service form submit
$(".recursive").hide();
if ($("input[name='is_recursive']").is(":checked")) {
  $(".recursive").show();
}
$("#is_recursive").on("click", function () {
  $(".recursive").hide();
  if ($("input[name='is_recursive']").is(":checked")) {
    $(".recursive").show();
  }
});

$("#service_form").on("submit", function (e) {
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
        window.location = base_url + "/admin/services";
      }
    },
  });
});

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
      '" data-tax_id= ' +
      products["tax_ids"] +
      ' data-is_tax_included="' +
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
    localStorage.getItem("cart" + session_business_id) !== null
      ? JSON.parse(cart)
      : null;
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

  let last_order_id = $("#pos_quick_invoice").data("id");
  if (last_order_id != "") {
    $("#pos_quick_invoice").data("id", "");
    $("#pos_quick_invoice").addClass("d-none");
  }
  display_cart();
  final_total();
}

$(document).on("change", ".cart-quantity-input-new", function (e) {
  this.value = this.value.replace(/[^0-9.]/g, ""); // Allow numbers and a decimal point
  this.value = this.value.replace(/^(\d*\.)(.*)\./g, "$1$2"); // Ensure only one decimal point

  var variant_id = $(this).siblings().val();
  var quantity = $(this).val();
  var data = quantity;

  update_quantity(data, variant_id);
});
function display_cart() {
  var session_business_id = $("#business_id").val();
  var cart = localStorage.getItem("cart" + session_business_id);
  cart =
    localStorage.getItem("cart" + session_business_id) !== null
      ? JSON.parse(cart)
      : null;
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
                            <span class="cart-price">${
                              currency + parseFloat(item.price).toLocaleString()
                            }</span>
                        </div>
                        <div class="col">
                        <div class="input-group-prepend">
                            <input type="hidden" class="product-variant" name="variant_ids[]" type="number" value=${
                              item.product_variant_id
                            }>
                            <button type="button" class="cart-quantity-input btn btn-sm btn-secondary" data-operation="minus"><i class="fas fa-minus"></i></button>
                                <input  class="form-control cart-input cart-quantity-input-new text-center p-0" step="0.1" name="quantity[]" id="quantity${
                                  item.product_variant_id
                                }" data-qty="${item.quantity}"  value="${
        item.quantity
      }">
                                <button type="button" class="cart-quantity-input btn btn-sm btn-secondary" data-operation="plus"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col">
                            <button class="btn btn-sm btn-danger remove-cart-item" data-business_id=${
                              item.business_id
                            } data-variant_id=${
        item.product_variant_id
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
    localStorage.getItem("cart" + session_business_id) !== null
      ? JSON.parse(cart)
      : null;
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
    localStorage.getItem("cart" + session_business_id) !== null
      ? JSON.parse(cart)
      : null;
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
          $("#pos_quick_invoice").data("id", order_id);
          $("#pos_quick_invoice").removeClass("d-none");
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

        get_todays_stats();
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
        $("#status_modal").modal("hide");
      }
    },
  });
});

// set delivery boy for order item
function set_delivery_boy(e) {
  var deliveryboy = $(e).find("option:selected").val();
  var order_id = $(e).find(":selected").attr("data-order_id");
  var type = $(e).find(":selected").attr("data-type");

  $.ajax({
    type: "get",
    url: site_url + "/admin/orders/set_delivery_boy/",
    data: {
      deliveryboy: deliveryboy,
      order_id: order_id,
      type: type,
    },
    cache: false,
    dataType: "json",
    success: function (result) {
      if (result.error == false) {
        iziToast.success({
          title: "Success!",
          message: result.message,
          position: "topRight",
        });
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
}
$(".delivery_boy").on("change", function () {
  set_delivery_boy(this);
});
// order-details update status of ordered item

function update_order_status(e) {
  var status = $(e).find("option:selected").val();
  var order_id = $(e).find(":selected").attr("data-order_id");
  var type = $(e).find(":selected").attr("data-type");

  $.ajax({
    type: "get",
    url: site_url + "/admin/orders/update_order_status/",
    data: {
      status: status,
      order_id: order_id,
      type: type,
    },
    cache: false,
    dataType: "json",
    success: function (result) {
      if (result.error == false) {
        iziToast.success({
          title: "Success!",
          message: result.message,
          position: "topRight",
        });
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
}
$(".status_update").on("change", function () {
  update_order_status(this);
});
// bulk update status

function show_message(prefix = "Great!", message, type = "success") {
  Swal.fire(prefix, message, type);
}
// bulk status update of order items
var item_id = [];

function update_bulk_status(item_id) {
  var bulk_status;
  var type;
  var order_id;
  bulk_status = $(".status_bulk").find("option:selected").val();
  order_id = $(".status_bulk").find("option:selected").attr("data-order_id");
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
  var response = [item_id, bulk_status, type, order_id];
  return response;
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
    };
    $.ajax({
      type: "post",
      url: base_url + "/delivery_boy/orders/update_status_bulk",
      data: request_body,
      cache: false,
      dataType: "json",
      success: function (result) {
        csrf_token = result["csrf_token"];
        csrf_hash = result["csrf_hash"];
        if (result.error == true) {
          showToastMessage(result.message, result.type);
        } else {
          showToastMessage(result.message, result.type);
          location.reload();
        }
      },
    });
  }
});

$(function () {
  $(".status_order_bulk").on("click", function () {
    if (this.checked) {
      var checked = $(".status_order").prop("checked", this.checked);
      $.each(checked, function (i, checked) {
        var id = checked.value;
        item_id.push(id);
      });
    } else {
      var checked = $(".status_order").prop("checked", false);
      item_id = [];
    }
  });
  $(".status_order").on("click", function () {
    if (this.checked) {
      var id = $(this).val();
      item_id.push(id);
      $(".status_order_bulk").prop("checked", false);
    } else {
      var id = $(this).val();
      item_id.pop(id);
    }
  });
});

// create payment modal event
$(document).on("show.bs.modal", "#create_payment", function (event) {
  var triggerElement = $(event.relatedTarget);
  var current_selected_variant = triggerElement;
  var order_id = $(current_selected_variant).data("order_id");
  var customer_id = $(current_selected_variant).data("customer_id");
  var supplier_id = $(current_selected_variant).data("supplier_id");
  $('input[name="order_id"]').val(order_id);
  $('input[name="customer_id"]').val(customer_id);
  $('input[name="supplier_id"]').val(supplier_id);
  $('input[name="order_type"]').val();
});
$("#amount").on("input", function () {
  let value = $(this).val();
  // Regex to match a valid float number
  let validFloatPattern = /^\d*\.?\d*$/;

  if (!validFloatPattern.test(value)) {
    // If input is invalid, remove the last entered character
    $(this).val(value.slice(0, -1));
    iziToast.error({
      title: "Error!",
      message: "Only positive float numbers are allowed!",
      position: "topRight",
    });
    return;
  }

  // Prevent negative values
  if (parseFloat(value) == -1 || parseFloat(value) < -1) {
    $(this).val("");
    iziToast.error({
      title: "Error!",
      message: "Negative value is not allowed!",
      position: "topRight",
    });
  }
});
$(".create_order_payment").on("submit", function (e) {
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
        Object.keys(result.message).map((key) => {
          var message = "";
          $("#create_payment")
            .find(".modal-body")
            .prepend(
              '<div class="alert alert-danger">' +
                result["message"][key] +
                "</div>"
            );
          $("#create_payment").find(".alert-danger").delay(4000).fadeOut();
          iziToast.error({
            title: "Error!",
            message: result.message[key],
            position: "topRight",
          });
        });
      } else {
        setTimeout(function () {
          location.reload();
        }, 600);
        iziToast.success({
          title: "Success!",
          message: result.message,
          position: "topRight",
        });
      }
    },
  });
});

$(".transaction_id").hide();
$("#payment_mode").on("change", function () {
  var type = $(this).find("option:selected").val();
  if (type == "other") {
    var html =
      ' <label for="payment_method_name">Enter Payment Method Name</label><span class="asterisk text-danger"> *</span>' +
      '<input type="text" class="form-control" id="payment_method_name" name="payment_method_name" placeholder="">';
    $("#type").append(html);
    $(".transaction_id").show();
  } else if (type == "cash") {
    $(".transaction_id").hide();
  } else {
    $("#type").html("");
    $(".transaction_id").show();
  }
});
$("#payment_type").on("change", function () {
  var type = $(this).find("option:selected").val();
  console.log(type);

  if (type == "other") {
    var html =
      ' <label for="payment_method_name">Enter Payment Method Name</label><span class="asterisk text-danger"> *</span>' +
      '<input type="text" class="form-control" id="payment_method_name" name="payment_method_name" placeholder="">';
    $("#payment_method_name_type").append(html);
    $(".transaction_id").show();
  } else if (type == "cash" || type == "wallet") {
    $(".transaction_id").hide();
  } else {
    $("#payment_method_name_type").html("");
    $(".transaction_id").show();
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
// subscription packages js

$(".free_package").on("click", function () {
  var user_id = $(this).data("user_id");
  var package_id = $(this).data("package_id");
  var tenure = $(this).data("tenure");
  var months = $(this).data("months");
  var price = $(this).data("price");
  var transaction_id = "0";
  const request_body = {
    [csrf_token]: csrf_hash,
    user_id: user_id,
    txn_id: transaction_id,
    package_id: package_id,
    months: months,
    tenure: tenure,
    price: price,
  };
  $.ajax({
    type: "post",
    url: site_url + "/admin/subscription/free_subscription",
    data: request_body,
    dataType: "json",
    success: function (result) {
      csrf_token = result["csrf_token"];
      csrf_hash = result["csrf_hash"];
      console.log(result);
      if (result.error == true) {
        location.href = base_url + "/admin/payments/payment_failed";
        showToastMessage(result.message, "error");
      } else {
        location.href = base_url + "/admin/payments/payment_success";
        iziToast.success({
          title: "Success!",
          message: result.message,
          position: "topRight",
        });
      }
    },
  });
});

$(get_tenure_id);
var tenure_id;

function get_tenure_id() {
  tenure_id = $(this).find(":selected").attr("data-tenure_id");
}
$(".tenures").on("change", function () {
  var id = $(this).attr("data-package_id");
  tenure_id = $(this).find(":selected").attr("data-tenure_id");
  var discount_value = $(this).find(":selected").attr("data-discount");
  var price = $(this).find(":selected").attr("data-price");
  var tenure_name = $(this).find(":selected").text();

  var status;
  var icon;
  if (discount_value == "0") {
    status = "bg-danger";
    icon = " fa-times";
  } else {
    status = "bg-success";
    icon = " fa-check";
  }
  var myvar =
    '<div class="pricing-item  ">' +
    '<div class="pricing-item-icon ' +
    status +
    '"><i class="fa ' +
    icon +
    '"></i></div>' +
    '<div class="pricing-item-label">Discounted price' +
    '<span class="discount_price"> ' +
    discount_value +
    "</span>" +
    "</div>" +
    "</div>";
  $("#price" + id).empty(this);
  $("#price" + id).append(this.value);
  $("#discount_price" + id)
    .children()
    .last()
    .remove();
  $("#discount_price" + id).append(myvar);
  if (discount_value == 0) {
    var price = $(this).find(":selected").attr("data-price");
    $("#price" + id).empty(price);
    $("#price" + id).append(price);
  } else {
    var discount =
      discount_value +
      ' <small class="discount-font">(<del>₹ ' +
      price +
      "</del>)</small>";
    $("#price" + id).empty(discount);
    $("#price" + id).append(discount);
  }
});
$(document).on("show.bs.modal", "#customer_status", function (event) {
  var triggerElement = $(event.relatedTarget);
  var current_selected_variant = triggerElement;
  var customer_id = $(current_selected_variant).data("id");
  $('input[name="customer_id"]').val(customer_id);
});

// update customers status
$(document).on("submit", "#customer_status", function (e) {
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
        location.reload();
        iziToast.success({
          title: "Success!",
          message: result.message,
          position: "topRight",
        });
      }
    },
  });
});

$(document).on("show.bs.modal", "#customers_services", function (event) {
  var triggerElement = $(event.relatedTarget);
  var current_selected_variant = triggerElement;
  var customer_id = $(current_selected_variant).data("customer_id");
  var existing_url = $(this).find("#customers_services_table").data("url");
  if (existing_url.indexOf("?") > -1) {
    var temp = $(existing_url).text().split("?");
    var new_url = temp[0] + "?customer_id=" + customer_id;
  } else {
    var new_url = existing_url + "?customer_id=" + customer_id;
  }
  $("#customers_services_table").bootstrapTable("refreshOptions", {
    url: new_url,
  });
});

// delivery boy register
$(document).on("submit", "#register_deliveryboy", function (e) {
  e.preventDefault();
  var business_id = $("#business_id").val();
  if (business_id == "0" || business_id == "") {
    if (!confirm("Please Add/Select your BUSINESS first!")) {
      return false;
    }
  } else {
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
          iziToast.success({
            title: "Success!",
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
      },
    });
  }
});

$(document).on("show.bs.modal", "#deliveryboy_register", function (event) {
  var triggerElement = $(event.relatedTarget);
  var current_selected_variant = triggerElement;
  var deliveryboy_id = $(current_selected_variant).data("id");
  var name = $(current_selected_variant).data("name");
  var identity = $(current_selected_variant).data("identity");
  var email = $(current_selected_variant).data("email");
  if (deliveryboy_id == undefined) {
    return;
  }
  $.ajax({
    type: "get",
    url: site_url + "/admin/delivery_boys/count",
    data: {
      id: deliveryboy_id,
    },
    cache: false,
    dataType: "json",
    success: function (result) {
      if (result.error == false) {
        $('input[name="business_id[]"]').val(result.business_id);
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
  $('input[name="first_name"]').val(name);
  $('input[name="id"]').val(deliveryboy_id);
  $('input[name="identity"]').val(identity);
  $('input[name="email"]').val(email);
});

$(document).on("submit", "#register_customer_form", function (e) {
  e.preventDefault();
  var business_id = $("#business_id").val();
  if (business_id == "0" || business_id == "") {
    if (!confirm("Please Add/Select your BUSINESS first!")) {
      return false;
    }
  } else {
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
          iziToast.success({
            title: "Success!",
            message: result.message,
            position: "topRight",
          });
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
  }
});

$("#customers_table").on("check.bs.table", function (e, row) {
  e.preventDefault();
  console.log(row);
  $("#customer_id").val(row.id);
  $("#name").val(row.customer_name);
  $("#identity").val(row.mobile);
  $("#identity").attr("readonly", true);

  $("#email").val(row.email);
  $("#user_id").val(row.id);

  if (row.active == 1) {
    $("#status").attr("checked", true);
  } else {
    $("#status").attr("checked", false);
  }
});

// filter orders list
var start_date = "";
var end_date = "";
var payment_status_filter = "";
var order_type_filter = "";
$("#payment_status_filter").on("change", function () {
  payment_status_filter = $(this).find("option:selected").val();
});

$("#order_type_filter").on("change", function () {
  order_type_filter = $(this).find("option:selected").val();
});
$(function () {
  $('input[name="date_range"]').daterangepicker(
    {
      opens: "left",
    },
    function (start, end) {
      start_date = start.format("YYYY-MM-DD");
      end_date = end.format("YYYY-MM-DD");
    }
  );
});
$("#date_range").on("change", function () {});

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

$("#filter").on("click", function (e) {
  $("#orders_items_table").bootstrapTable("refresh");
});

$(document).on("click", ".set_field_delivery_boy", function () {
  let formdata = new FormData();
  formdata.append(csrf_token, csrf_hash);
  let url = $(this).data("route");
  let delivery_boy_id = $(this).data("id");
  formdata.append("id", delivery_boy_id);
  $.ajax({
    type: "POST",
    url: url,
    cache: false,
    processData: false,
    contentType: false,
    data: formdata,
    success: function (result) {
      let data = result.data;
      if (result.error == false) {
        $("#name").val(data.name);
        $("#identity").val(data.mobile);
        $("#email").val(data.email);
        $("#delivery_boy_id").val(data.user_id);

        let permissions = JSON.parse(data.permissions);
        let business_data = data.business_data;

        let business_id = data.buisness_ids;

        let temp = "";
        for (const business of business_data) {
          temp = business.class_name;
          if (temp == `business_${business_id}`) {
            $("." + `${temp}`).attr("checked", true);
          }
        }

        if (permissions.customer_permission == "1") {
          $("#customer_permission").attr("checked", true);
        } else {
          $("#customer_permission").attr("checked", false);
        }
        if (permissions.transaction_permission == "1") {
          $("#transaction_permission").attr("checked", true);
        } else {
          $("#transaction_permission").attr("checked", false);
        }
        if (permissions.orders_permission == "1") {
          $("#orders_permission").attr("checked", true);
        } else {
          $("#orders_permission").attr("checked", false);
        }
      } else {
        iziToast.error({
          title: "Error!",
          message: result.message,
          position: "topRight",
        });
      }
    },
  });
});

$("#delivery_boys_table").on("check.bs.table", function (e, row) {
  e.preventDefault();
  $("input[name='business_id[]']:checkbox").attr("checked", false);
  $("input[name='status']:checkbox").attr("checked", true);
  $("#name").val(row.name);
  $("#identity").val(row.mobile);
  $("#email").val(row.email);
  $("#delivery_boy_id").val(row.id);
  if (row.permissions.customer_permission == "1") {
    $("#customer permission").attr("checked", true);
  } else {
    $("#customer permission").attr("checked", false);
  }
  if (row.permissions.transaction_permission == "1") {
    $("#transaction permission").attr("checked", true);
  } else {
    $("#transaction permission").attr("checked", false);
  }
  if (row.permissions.orders_permission == "1") {
    $("#orders permission").attr("checked", true);
  } else {
    $("#orders permission").attr("checked", false);
  }

  var assigned_b_id = row.assigned_b_id;
  if (assigned_b_id.length > 1) {
    var b_id = assigned_b_id.split(",");
    $.each(b_id, function (i, b_id) {
      if ($("#" + b_id).val() == b_id) {
        $("#" + b_id).attr("checked", true);
      }
    });
  } else {
    if ($("#" + assigned_b_id).val() == assigned_b_id) {
      $("#" + assigned_b_id).attr("checked", true);
    }
  }

  if (row.active == 1) {
    $("#status").attr("checked", true);
  } else {
    $("#status").attr("checked", false);
  }
});

/*Search AJAX Users in POS*/
$(document).ready(function () {
  $(".select_user").select2({
    ajax: {
      url: site_url + "admin/orders/get_users",
      dataType: "json",
      data: function (params) {
        var query = {
          search: params.term,
        };
        return query;
      },
      processResults: function (response) {
        return {
          results: response.data,
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
    "<div class='select2-result-postsitory clearfix'>" +
      "<div class='select2-result-postsitory__meta'>" +
      "<strong>" +
      post.text +
      "</strong><span> | </span>" +
      "<strong>" +
      post.number +
      "</strong><span> | </span>" +
      "<strong>" +
      post.email +
      "</strong>" +
      "</div>" +
      "</div>" +
      "</div>"
  );

  return $container;
}

function formatPostSelection(post) {
  return post.text;
}
let userId = $("#user_id").val();

// subscription transaction filter
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
$("#transaction_filter_btn").on("click", function (e) {
  $("#vendors_transactions_table").bootstrapTable("refresh");
});

function refresh_table(id) {
  $("#" + id).bootstrapTable("refresh");
}

// recursive_services table for subscription
$(document).on("show.bs.modal", "#recursive_services", function (event) {
  var triggerElement = $(event.relatedTarget);
  var current_selected_variant = triggerElement;
  var id = $(current_selected_variant).data("service_id");

  var existing_url = $(this)
    .find("#customers_list_of_services_table")
    .data("url");
  if (existing_url.indexOf("?") > -1) {
    var temp = $(existing_url).text().split("?");
    var new_url = temp[0] + "?service_id=" + id;
  } else {
    var new_url = existing_url + "?service_id=" + id;
  }
  $("#customers_list_of_services_table").bootstrapTable("refreshOptions", {
    url: new_url,
  });
});

function remove_subscription(e) {
  if (!confirm("Are you sure want to delete?")) {
    return false;
  }
  var subscription_id = $(e).attr("data-sub_id");
  $.ajax({
    type: "get",
    url:
      site_url +
      "/admin/customers_subscription/remove_subscription/" +
      subscription_id,
    cache: false,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (result) {
      if (result.error == false) {
        iziToast.success({
          title: "Success!",
          message: result.message,
          position: "topRight",
        });
        $("#customers_services").bootstrapTable("refresh");
      } else {
        iziToast.error({
          title: "Error!",
          message: result.message,
          position: "topRight",
        });
      }
    },
  });
}

//  dashboard chart
if ($("#myChart").length > 0) {
  var total_sale = [];
  var month_name;
  var data = [];

  $.ajax({
    type: "get",
    url: site_url + "/admin/home/fetch_sales",
    cache: false,
    dataType: "json",
    success: function (result) {
      total_sale = result.total_sale;
      month_name = result.month_name;
      var data = {
        labels: month_name,
        datasets: [
          {
            label: "sale",
            backgroundColor: [
              "rgba(255, 99, 132, 0.2)",
              "rgba(255, 159, 64, 0.2)",
              "rgba(255, 205, 86, 0.2)",
              "rgba(75, 192, 192, 0.2)",
              "rgba(54, 162, 235, 0.2)",
              "rgba(153, 102, 255, 0.2)",
              "rgba(201, 203, 207, 0.2)",
            ],
            borderColor: [
              "rgb(255, 99, 132)",
              "rgb(255, 159, 64)",
              "rgb(255, 205, 86)",
              "rgb(75, 192, 192)",
              "rgb(54, 162, 235)",
              "rgb(153, 102, 255)",
              "rgb(201, 203, 207)",
            ],
            borderWidth: 1,
            data: total_sale,
          },
        ],
      };

      var config = {
        type: "bar",
        data: data,
        options: {
          responsive: true,
        },
      };
      var myChart = new Chart(document.getElementById("myChart"), config);
    },
  });
}
if ($("#sales-per-warehouse-chart").length > 0) {
  $.ajax({
    type: "get",
    url: site_url + "/admin/home/fetch_warehouse_sales",
    cache: false,
    dataType: "json",
    success: function (result) {
      var datasets = [];
      var labels = []; // Month labels
      var backgroundColors = [
        "rgba(255, 99, 132, 0.2)",
        "rgba(255, 159, 64, 0.2)",
        "rgba(255, 205, 86, 0.2)",
        "rgba(75, 192, 192, 0.2)",
        "rgba(54, 162, 235, 0.2)",
        "rgba(153, 102, 255, 0.2)",
        "rgba(201, 203, 207, 0.2)",
      ];
      var borderColors = [
        "rgb(255, 99, 132)",
        "rgb(255, 159, 64)",
        "rgb(255, 205, 86)",
        "rgb(75, 192, 192)",
        "rgb(54, 162, 235)",
        "rgb(153, 102, 255)",
        "rgb(201, 203, 207)",
      ];

      // Iterate through each warehouse in the result
      var colorIndex = 0;
      $.each(result, function (warehouse_id, warehouse_data) {
        if (labels.length === 0) {
          // Set the month labels only once, as all warehouses will share the same months
          labels = warehouse_data.month_name;
        }

        datasets.push({
          label: warehouse_data.warehouse_name + " Sales",
          backgroundColor:
            backgroundColors[colorIndex % backgroundColors.length],
          borderColor: borderColors[colorIndex % borderColors.length],
          borderWidth: 1,
          data: warehouse_data.total_sales,
        });

        colorIndex++;
      });

      // Create chart data
      var chartData = {
        labels: labels,
        datasets: datasets,
      };

      // Create chart config
      var config = {
        type: "bar",
        data: chartData,
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
            },
          },
        },
      };

      // Render the chart
      var sales_per_warehouse_chart = new Chart(
        document.getElementById("sales-per-warehouse-chart"),
        config
      );
    },
  });
}

// doughnut chart
if ($("#pieChart").length > 0) {
  $.ajax({
    type: "get",
    url: site_url + "/admin/home/fetch_data",
    cache: false,
    dataType: "json",
    success: function (result) {
      const data = {
        labels: ["sale", "orders", "customers"],
        datasets: [
          {
            label: "sale",
            data: [result.sales, result.orders, result.customer],
            backgroundColor: [
              "rgb(255, 99, 132)",
              "rgb(54, 162, 235)",
              "rgb(255, 205, 86)",
            ],
            hoverOffset: 4,
          },
        ],
      };

      const config = {
        type: "doughnut",
        data: data,
      };
      const myChart = new Chart(document.getElementById("pieChart"), config);
    },
  });
}

function set_locale(language_code) {
  $.ajax({
    url: base_url + "/admin/languages/change/" + language_code,
    type: "GET",
    success: function (result) {},
  }).then(() => {
    location.reload();
  });
}

$("#send_invoice").on("click", function () {
  var email = $(this).attr("data-email");
  var order_id = $(this).attr("data-order_id");
  const request = {
    [csrf_token]: csrf_hash,
    email_id: email,
    order_id: order_id,
  };
  $.ajax({
    type: "post",
    url: site_url + "/admin/invoices/send",
    data: request,
    dataType: "json",
    success: function (result) {
      csrf_token = result["csrf_token"];
      csrf_hash = result["csrf_hash"];
      if (result.error == false) {
        showToastMessage(result.message, "success");
      } else {
        showToastMessage(result.message, "error");
      }
    },
  });
});
let stock;
$(".stock_btn").on("click", function (e) {
  e.preventDefault();
  stock = $(this).attr("data-flag");
  window.location.href = base_url + "/admin/products/stock/" + stock;
});

function stock_params(params) {
  return {
    stock: params.stock,
    search: params.search,
    limit: params.limit,
    sort: params.sort,
    order: params.order,
    offset: params.offset,
  };
}

// search suppliers using ajax
$(document).ready(function () {
  $(".select_supplier").select2({
    ajax: {
      url: site_url + "admin/purchases/get_suppliers",
      dataType: "json",
      data: function (params) {
        var query = {
          search: params.term,
        };
        return query;
      },
      processResults: function (response) {
        return {
          results: response.data,
        };
      },
      cache: true,
    },
    placeholder: "Search for a Supplier",
    templateResult: formatPostSuppliers,
    templateSelection: SuppliersSelection,
  });
});

function formatPostSuppliers(p) {
  if (p.loading) {
    return p.text;
  }
  var $supplier = $(
    "<div class='select2-result-postsitory clearfix'>" +
      "<div class='select2-result-postsitory__meta'>" +
      "<strong>" +
      p.text +
      "</strong><span> | </span>" +
      "<strong>" +
      p.balance +
      "</strong>" +
      "</div>" +
      "</div>"
  );
  return $supplier;
}

function SuppliersSelection(p) {
  return p.text;
}

// search products
$(document).ready(function () {
  $(".search_products").select2({
    ajax: {
      url: site_url + "variants/products_variants_list",
      dataType: "json",
      delay: 350, // milliseconds before sending request

     
      data: function (params) {
        var query = {
          search: params.term,
          csrf_test_name: $('meta[name="csrf-token"]').attr("content"),
        };
        return query;
      },
      processResults: function (response) {
        return {
          results: response.variants,
        };
      },
      cache: true,
    },
    placeholder: "Search for a Products",
    templateResult: formatPostProducts,
    templateSelection: ProductsSelection,
  });
});

function formatPostProducts(p) {
  if (p.loading) {
    return p.text;
  }

  var str = p.name + "-" + p.variant_name;
  str = str.toLowerCase().replace(/\b[a-z]/g, function (letter) {
    return letter.toUpperCase();
  });
  var $products = $(
    "<div class='select2-result-postsitory clearfix'>" +
      "<div class='select2-result-postsitory__meta'>" +
      '<span><img src="' +
      site_url +
      p.image +
      '" width="28px" class="img-fluid"/> <strong>' +
      str +
      "</strong></span><br>" +
      "<div class='select2-result-repository__stargazers'><i class='fa fa-flag'></i> In " +
      p.category +
      "</div>" +
      "</div>" +
      "</div>"
  );

  return $products;
}

function ProductsSelection(p) {
  return p.variant_name;
}
function formatState(p) {
  if (!p.id) {
    return p.text;
  }

  var optimage = $(p.element).attr("data-image");
  if (!optimage) {
    return p.text;
  } else {
    var $opt = $(
      '<span><img src="' + optimage + '" width="28px" /> ' + p.text + "</span>"
    );
    return $opt;
  }
}

$(document).on("click", ".edit_btn", function (e) {
  e.preventDefault();
  var url = $(this).data("url");

  $(".edit-modal-lg")
    .modal("show")
    .find(".modal-body")
    .load(base_url + "/" + url + " .form-submit-event", function () {
      if ($("input[data-bootstrap-switch]").length) {
        $("input[data-bootstrap-switch]").each(function () {
          $("input[data-bootstrap-switch]").bootstrapSwitch();
        });
      }
    });
});

// $(document).on("submit", "#purchase_form", function (e) {
//   e.preventDefault();
//   var formData = new FormData(this);
//   console.log(formData);
//   formData.append(csrf_token, csrf_hash);
//   $.ajax({
//     type: "POST",
//     url: this.action,
//     dataType: "json",
//     data: formData,
//     processData: false,
//     contentType: false,

//     success: function (result) {
//       csrf_token = result["csrf_token"];
//       csrf_hash = result["csrf_hash"];
//       if (result.error == false) {
//         setTimeout(() => {
//           showToastMessage(result.message, "success");
//           location.href = base_url + "/admin/purchases";
//         }, 1000);
//       } else {
//         Object.keys(result.message).map((key) => {
//           showToastMessage(result["message"][key], "error");
//         });
//         return;
//       }
//     },
//   });
// });

// for purchase
var variant_data = [];
var qty;
var discount;
var price;
var count = 1;

if ($("#purchase_form").length > 0) {
  $(document).ready(function () {
    // Hide hidden fields
    $('.dropdown-menu .dropdown-item-marker input[data-field="variant_id"]')
      .closest("label")
      .hide();

    // Initialize flatpickr once for all future expire inputs
    $(document).on('focus', '.expire', function () {
      $(this).flatpickr({
        dateFormat: "Y-m-d",
        allowInput: true,
        minDate: "today"
      });
    });

    // Set up event handlers once
    setupPurchaseOrderEventHandlers();
  });

  $(document).on("change", ".search_products", function (e) {
    e.preventDefault();
  const select2Data = $(".search_products").select2("data");
  
  if (!select2Data || select2Data.length === 0) {
    showToastMessage("Please select a valid product", "error");
    return;
  }
  
  const data = select2Data[0];
  console.log('variant data: ', variant_data);
  console.log('data: ', data);
  // Check for duplicates - now only checks exact variant_id matches
  if (variant_data.some(v => v.id == data.variant_id)) {
    const productName = [data.name, data.variant_name].filter(Boolean).join(" - ");
    showToastMessage(`The exact variant ${productName} is already in the list`, "error");
    return;
  }

  // Rest of your code remains the same...
  variant_data.push({
    id: data.variant_id,
    name: data.name,
    variant: data.variant_name,
    price: data.purchase_price || 0,
      
  });
  $('input[name="products"]').val(JSON.stringify(variant_data));

    const price = parseFloat(data.purchase_price) || 0;
    const sellPrice = parseFloat(data.sell_price) || 0;

    const tableRow = {
      name: [data.name, data.variant_name].filter(Boolean).join(" - "),
      image: data.image ? 
        `<img src="${site_url}${data.image}" width="60" height="60" class="img-thumbnail" alt="Product Image">` : 
        '<div class="no-image">No Image</div>',
      sr: count++,
      id: `<input type="hidden" name="variant_ids[]" value="${data.variant_id}">${data.variant_id}`,
      quantity: createInputField('number', 'qty', data.variant_id, 1, { min: 1, step: 1, 'data-price': price }),
      price: createInputField('number', 'price', data.variant_id, price, { min: 0.01, step: 0.01 }),
      sell_price: createInputField('number', 'sell_price', data.variant_id, sellPrice, { min: 0, step: 0.01 }),
      discount: createInputField('number', 'discount', data.variant_id, 0, { min: 0, step: 0.01, max: price }),
      total: `<span class="table_price">${price.toFixed(2)}</span>`,
      expire: `<input type="text" class="form-control expire" name="expire[${data.variant_id}]" 
                     placeholder="YYYY-MM-DD" autocomplete="off">`,
      actions: '<button class="btn btn-sm btn-danger remove-row"><i class="fa fa-trash"></i></button>'
    };

    $("#purchase_order").bootstrapTable("insertRow", {
      index: 0,
      row: tableRow
    });

    $('[data-toggle="tooltip"]').tooltip();
    purchase_total();
  });
}


function createInputField(type, name, variantId, value, attributes = {}) {
  const attrs = Object.entries(attributes)
    .map(([key, val]) => `${key}="${val}"`)
    .join(" ");
  return `<input type="${type}" class="form-control ${name}" 
          name="${name}[${variantId}]" value="${value}"
          ${attrs}>`;
}

function setupPurchaseOrderEventHandlers() {
  // Validate sell price
  $("#purchase_order").on("input", ".sell_price", function () {
    var val = parseFloat($(this).val()) || 0;
    if (val < 0) {
      showToastMessage("Sell price cannot be negative.", "error");
      $(this).val("");
    }
    purchase_total();
  });

  // Handle price calculations
  $("#purchase_order").on("input", ".price, .discount, .qty", function () {
    var row = $(this).closest("tr");
    var qty = parseFloat(row.find(".qty").val()) || 0;
    var price = parseFloat(row.find(".price").val()) || 0;
    var discount = parseFloat(row.find(".discount").val()) || 0;

    // Validate inputs
    if (price < 0) {
      showToastMessage("Price must be greater than 0.", "error");
      row.find(".price").val("");
      return;
    }
    
    if (qty <= 0) {
      showToastMessage("Quantity must be greater than 0.", "error");
      row.find(".qty").val("");
      return;
    }

    var subtotal = qty * price;
    
    if (discount < 0 || discount > subtotal) {
      showToastMessage(
        `Discount must be between 0 and ${subtotal.toFixed(2)}.`,
        "error"
      );
      row.find(".discount").val("0");
      discount = 0;
    }

    row.find(".table_price").text((subtotal - discount).toFixed(2));
    purchase_total();
  });

  // Validate expiration date
  $("#purchase_order").on("change", ".expire", function () {
    var val = $(this).val();
    if (val && val < new Date().toISOString().split("T")[0]) {
      showToastMessage("Expiration date cannot be in the past.", "error");
      $(this).val("");
    }
  });
}

function product_details(e) {
  variant_data.push({
    id: data.variant_id,
    name: data.variant_name,
  });

  $('input[name="products"]').val(JSON.stringify(variant_data));
}
function newFunction() {
  $.fn.editable.defaults.mode = "inline";
  $(document).ready(function () {
    $("#username").editable();
  });
}

$(document).on("keyup", ".qty", function (e) {
  subTotal(this);
  purchase_total();
});
$(document).on("change", ".qty", function (e) {
  subTotal(this);
  purchase_total();
});
$(document).on("change", ".price", function (e) {
  settlePrice(this);
  purchase_total();
});
$(document).on("keyup", ".price", function (e) {
  settlePrice(this);
  purchase_total();
});
$(document).on("keyup", ".discount", function (e) {
  settleDisount(this);
  purchase_total();
});
$(document).on("change", ".discount", function (e) {
  settleDisount(this);
  purchase_total();
});

function subTotal(e) {
  $("#sub_total").html("");

  var qty = $(e).val();
  var table_subtotal =
    e.parentElement.parentElement.getElementsByClassName("table_price");
  var price = $(
    e.parentElement.parentElement.getElementsByClassName("price")
  ).val();
  var discount = $(
    e.parentElement.parentElement.getElementsByClassName("discount")
  ).val();
  $(table_subtotal).html("0");
  if (qty != 0 && qty != null) {
    var sub_total = parseFloat(price) * parseFloat(qty);
    $(table_subtotal).html(sub_total);
  }
  if (discount != 0 && discount != null) {
    var sub_total = parseFloat(price) * parseFloat(qty) - parseFloat(discount);
    $(table_subtotal).html(sub_total);
  }
}

function settlePrice(e) {
  var price = $(e).val();
  $("#sub_total").html("");
  var table_subtotal =
    e.parentElement.parentElement.getElementsByClassName("table_price");
  var price_class = $(e.parentElement.getElementsByClassName("price"));
  var qty = $(
    e.parentElement.parentElement.getElementsByClassName("qty")
  ).val();
  var discount = $(
    e.parentElement.parentElement.getElementsByClassName("discount")
  ).val();
  var price = $(price_class).val();
  var sub_total = parseFloat(price) * parseFloat(qty);
  $(table_subtotal).html(sub_total);
  if (price != 0 && price != null) {
    $(table_subtotal).html(sub_total);
    if (qty != 0 && qty != null) {
      sub_total = parseFloat(price) * parseFloat(qty);
      $(table_subtotal).html(sub_total);
    }
    if (discount != 0 && discount != null) {
      var sub_total =
        parseFloat(price) * parseFloat(qty) - parseFloat(discount);
      $(table_subtotal).html(sub_total);
    }
  }
}
var $table = $("#purchase_order");
var $remove = $("#remove");
let $sales_order_table = $("#sales_order");
$(function (e) {
  if ($("#purchase_form").length > 0) {
    // $table.on(
    //     "check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table",
    //     function () {
    //         $remove.prop("disabled", !$table.bootstrapTable("getSelections").length);
    //     }
    // );
    // $remove.click(function (e) {
    //     e.preventDefault();
    //     var ids = $.map($table.bootstrapTable("getSelections"), function (row) {
    //         return row.id;
    //     });

    //     $table.bootstrapTable("remove", {
    //         field: "id",
    //         values: ids,
    //     });
    //     purchase_total();
    //     $remove.prop("disabled", true);
    // });

    $table.on(
      "check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table",
      function () {
        // Enable/Disable the remove button based on selection
        $remove.prop(
          "disabled",
          !$table.bootstrapTable("getSelections").length
        );
      }
    );

    // Handle remove button click
    $remove.click(function (e) {
      e.preventDefault();

      // Get the IDs of selected rows
      var ids = $.map($table.bootstrapTable("getSelections"), function (row) {
        return row.id;
      });

      // Remove the selected rows from the table
      $table.bootstrapTable("remove", {
        field: "id",
        values: ids,
      });

      // Remove corresponding product details from the #products input field
      var products = JSON.parse($("input#products").val() || "[]"); // Get current product details (if any)
      // Filter out products that have an ID matching the removed rows
      var updatedProducts = products.filter(function (product) {
        return ids.indexOf(product.id) === -1; // Exclude products whose ID is in the `ids` array
      });

      // Update the #products input with the updated product details (serialized as JSON)
      $("input#products").val(JSON.stringify(updatedProducts));

      // ✅ Remove matching items from variant_data[]
      variant_data = variant_data.filter(function (variant) {
        return ids.indexOf(variant.id) === -1;
      });
      console.log('after delete: ',variant_data);
      // Call function to update any purchase totals or other details
      purchase_total();

      // Disable the remove button after the operation
      $remove.prop("disabled", true);
    });
  }

  if ($("#sales_order_form").length > 0) {
    $sales_order_table.on(
      "check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table",
      function () {
        $remove.prop(
          "disabled",
          !$sales_order_table.bootstrapTable("getSelections").length
        );
      }
    );
    $remove.click(function (e) {
      e.preventDefault();
      var ids = $.map(
        $sales_order_table.bootstrapTable("getSelections"),
        function (row) {
          return row.id;
        }
      );

      $sales_order_table.bootstrapTable("remove", {
        field: "id",
        values: ids,
      });

      // Remove corresponding product details from the #sale_product_id input field
      var sale_product_id = JSON.parse(
        $("input#sale_product_id").val() || "[]"
      ); // Get current product details (if any)

      // Filter out products that have an ID matching the removed rows
      var updatedSale_product_id = sale_product_id.filter(function (
        sale_product_id
      ) {
        return ids.indexOf(sale_product_id.variant_id) === -1; // Exclude products whose ID is in the `ids` array
      });

      // Update the #products input with the updated product details (serialized as JSON)
      $("input#sale_product_id").val(JSON.stringify(updatedSale_product_id));

      purchase_total();
      $remove.prop("disabled", true);
    });
  }
});

$(function (e) {
  $sales_order_table.on(
    "check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table",
    function () {
      $remove.prop(
        "disabled",
        !$sales_order_table.bootstrapTable("getSelections").length
      );
    }
  );
  $remove.click(function () {
    var ids = $.map(
      $sales_order_table.bootstrapTable("getSelections"),
      function (row) {
        return row.id;
      }
    );

    $sales_order_table.bootstrapTable("remove", {
      field: "id",
      values: ids,
    });
    purchase_total();
    $remove.prop("disabled", true);
  });
});

function settleDisount(e) {
  var discount = $(e).val();
  $("#sub_total").html("");
  var table_subtotal =
    e.parentElement.parentElement.getElementsByClassName("table_price");
  var qty = $(
    e.parentElement.parentElement.getElementsByClassName("qty")
  ).val();
  var price = $(
    e.parentElement.parentElement.getElementsByClassName("price")
  ).val();
  var sub_total = parseFloat(price) * parseFloat(qty);
  $(table_subtotal).html(sub_total);
  if (discount != 0 && discount != null) {
    sub_total = parseFloat(price) * parseFloat(qty) - parseFloat(discount);
    $(table_subtotal).html(sub_total);
  }
}
$(document).on("change", "#order_taxes", function (e) {
  purchase_total();
});
$(document).on("keyup", "#order_discount", function (e) {
  let value = $(this).val();
  value = value.replace(/[^0-9.]/g, "");
  if ((value.match(/\./g) || []).length > 1) {
    value = value.replace(/\.+$/, "");
  }
  $(this).val(value);
  purchase_total();
});
$(document).on("keyup", "#shipping", function (e) {
  let value = $(this).val();
  value = value.replace(/[^0-9.]/g, "");
  if ((value.match(/\./g) || []).length > 1) {
    value = value.replace(/\.+$/, "");
  }
  $(this).val(value);
  purchase_total();
});
$(".purchase-submit").on("click", function () {
  purchase_total();
});

/**
 *  Note : purchase_total function is responsible for managing total for both in Sales and Purchase
 */
function purchase_total() {
  var total = 0; // total purchase after row discount
  var final_total = 0;
  var sell_total = 0;
  var profit_total = 0;
  var currency = $("#sub_total").attr("data-currency");

  $(".table_price").each(function (i, el) {
    var row = $(el).closest("tr");

    var price = parseFloat(row.find(".price").val()) || 0;
    var qty = parseFloat(row.find(".qty").val()) || 0;
    var discountInput = row.find(".discount").val().trim();
    var discount = 0;

    if (discountInput.endsWith("%")) {
      var percentValue = parseFloat(discountInput.slice(0, -1));
      discount = (percentValue / 100) * (price * qty);
    } else {
      discount = parseFloat(discountInput) || 0;
    }

    var row_total = price * qty - discount;
    total += row_total;

    // sell price total
    var sell_price = parseFloat(row.find(".sell_price").val()) || 0;
    var row_sell_total = sell_price * qty;
    sell_total += row_sell_total;

    // profit total
    profit_total += row_sell_total - row_total;
  });

  var order_discount = parseFloat($("#order_discount").val()) || 0;
  var shipping = parseFloat($("#shipping").val()) || 0;

  // Apply order discount and shipping
  final_total = total - order_discount + shipping;
  profit_total = sell_total - final_total;
  // Update fields
  $("#sub_total").html(currency + final_total.toFixed(3));
  $('input[name="total"]').val(final_total.toFixed(3));

  // Show sell total and profit total
  $("#sell_total").html(currency + sell_total.toFixed(3));
  $("#profit_total").html(currency + profit_total.toFixed(3));
}

// purchase order status update bulk
$(".purchase_update_status_bulk ").on("click", function (e) {
  e.preventDefault();
  var item = update_bulk_status(item_id);
  if (!item) {
    return;
  } else {
    var item_ids = item[0];
    var status = item[1];
    var type = item[2];
    var order_id = item[3];
    const request_body = {
      [csrf_token]: csrf_hash,
      item_ids: item_ids,
      status: status,
      type: type,
      order_id: order_id,
    };
    $.ajax({
      type: "post",
      url: base_url + "/admin/purchases/update_status_bulk",
      data: request_body,
      cache: false,
      dataType: "json",
      success: function (result) {
        csrf_token = result["csrf_token"];
        csrf_hash = result["csrf_hash"];
        if (result.error == true) {
          showToastMessage(result.message, result.type);
        } else {
          showToastMessage(result.message, result.type);
          location.reload();
        }
      },
    });
  }
});

function update_order_status(e) {
  var status = $(e).find("option:selected").val();
  var order_id = $(e).find(":selected").attr("data-order_id");
  var type = $(e).find(":selected").attr("data-type");

  $.ajax({
    type: "get",
    url: site_url + "/admin/purchases/update_order_status/",
    data: {
      status: status,
      order_id: order_id,
      type: type,
    },
    cache: false,
    dataType: "json",
    success: function (result) {
      if (result.error == false) {
        iziToast.success({
          title: "Success!",
          message: result.message,
          position: "topRight",
        });
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
}
$(".purchase_status_update").on("change", function () {
  update_order_status(this);
});

$(document).on("submit", "#bulk_uploads_form", function (e) {
  e.preventDefault();
  console.log(this);
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
      if (result.error == false) {
        setTimeout(() => {
          showToastMessage(result.message, "success");
        }, 1000);
        setTimeout(() => {
          location.reload();
        }, 2000);
      } else {
        showToastMessage(result.message, "error");
        return;
      }
    },
  });
});

if ($("#charttest").length > 0) {
  var ctx = document.getElementById("charttest").getContext("2d");
  var total_sale = [];
  var month_name;
  var data = [];
  var myChart = [];
  var total_purchase = [];
  var month_name_purchase;
  var data_p = [];

  $.ajax({
    type: "get",
    url: site_url + "/admin/home/fetch_purchases",
    cache: false,
    dataType: "json",
    success: function (result) {
      console.log(result);

      total_sale = result.total_sale;
      month_name = result.month_name;
      total_purchase = result.total_purchases;
      month_name_purchase = result.month_name;
      var myChart = new Chart(ctx, {
        type: "bar",
        data: {
          labels: month_name,
          datasets: [
            {
              label: "sales",
              data: total_sale,
              borderWidth: 2,
              backgroundColor: "rgba(63,82,227,.8)",
              borderWidth: 0,
              borderColor: "transparent",
              pointBorderWidth: 0,
              pointRadius: 2.5,
              pointBackgroundColor: "transparent",
              pointHoverBackgroundColor: "rgba(63,82,227,.8)",
            },
            {
              label: "Purchase",
              data: total_purchase,
              borderWidth: 2,
              backgroundColor: "rgba(254,86,83,.7)",
              borderWidth: 0,
              borderColor: "transparent",
              pointBorderWidth: 0,
              pointRadius: 2.5,
              pointBackgroundColor: "transparent",
              pointHoverBackgroundColor: "rgba(254,86,83,.8)",
            },
          ],
        },
        options: {
          legend: {
            display: false,
          },
          scales: {
            yAxes: [
              {
                gridLines: {
                  // display: false,
                  drawBorder: false,
                  color: "#f2f2f2",
                },
                ticks: {
                  beginAtZero: true,
                  stepSize: 1500,
                  callback: function (value, index, values) {
                    return "$" + value;
                  },
                },
              },
            ],
            xAxes: [
              {
                gridLines: {
                  display: false,
                  tickMarkLength: 15,
                },
              },
            ],
          },
        },
      });
    },
  });
}

// fetch stock on select 2
$(document).ready(function () {
  $(".fetch_stock").select2({
    ajax: {
      url: site_url + "admin/products/fetch_stock",
      dataType: "json",
      data: function (params) {
        var query = {
          search: params.term,
        };
        return query;
      },
      processResults: function (response) {
        return {
          results: response.data,
        };
      },
      cache: true,
    },
    placeholder: "Search for a Products",
    templateResult: format,
    templateSelection: StockSelection,
  });
});

function format(p) {
  if (p.loading) {
    return p.text;
  }
  var str = p.name;
  str = str.toLowerCase().replace(/\b[a-z]/g, function (letter) {
    return letter.toUpperCase();
  });
  var $products = $(
    "<div class='select2-result-postsitory clearfix'>" +
      "<div class='select2-result-postsitory__meta'>" +
      '<span><img src="' +
      site_url +
      p.image +
      '" width="28px" class="img-fluid"/> <strong>' +
      str +
      "</strong></span><br>" +
      "</div>" +
      "</div>"
  );

  return $products;
}

function StockSelection(p) {
  return p.name;
}

// save stock adjustment
$(document).on("submit", "#stock_adjustment_form", function (e) {
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
      if (result.error == false) {
        setTimeout(() => {
          showToastMessage(result.message, "success");
          location.reload();
        }, 1000);
      } else {
        Object.keys(result.message).map((key) => {
          showToastMessage(result["message"][key], "error");
        });
        return;
      }
    },
  });
});

$(document).on("show.bs.modal", "#new_stock", function (event) {
  $(this).hide().show();
  var triggerElement = $(event.relatedTarget);
  var current_selected_variant = triggerElement;
  var id = $(current_selected_variant).data("product_id");
  var stock_management = $(current_selected_variant).data("stock_management");
  var stock = $(current_selected_variant).data("stock");
  var name = $(current_selected_variant).data("name");
  let variant_id = $(current_selected_variant).data("variant_id");
  var options;
  $('input[name="product"]').val(id);
  $('input[name="variant_id"]').val(variant_id);
  $('input[name="stock_management"]').val(stock_management);
  $('input[name="current_stock"]').val(stock);
  $('input[name="name"]').val(name);
  $("#fetch_stock_1").val(id).trigger("change");
});
$(document).on("show.bs.modal", "#transfer_stock", function (event) {
  $(this).hide().show();
  var triggerElement = $(event.relatedTarget);
  var current_selected_variant = triggerElement;

  let name = $(current_selected_variant).data("name");
  let variant_id = $(current_selected_variant).data("variant_id");
  console.log(variant_id);

  $('input[name="ts_variant_id"]').val(variant_id);

  $('input[name="ts_name"]').val(name);
});

$("#expenses_form").on("submit", function (e) {
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
        window.location = base_url + "/admin/expenses";
        showToastMessage(result.message, "success");
        console.log(window.location);
      }
    },
  });
});

$("#customer_form").on("submit", function (e) {
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
        window.location = base_url + "/admin/expenses";
        showToastMessage(result.message, "success");
        console.log(window.location);
      }
    },
  });
});

$("#expenses_type_form").on("submit", function (e) {
  e.preventDefault();
  var formData = new FormData(this);
  formData.append(csrf_token, csrf_hash);
  $.ajax({
    type: "POST",
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
        window.location = base_url + "/admin/expenses_type";
        showToastMessage(result.message, "success");
        console.log(window.location);
      }
    },
  });
});

$(document).on("show.bs.modal", "#expenses_modal", function (event) {
  var triggerElement = $(event.relatedTarget);
  var current_selected_variant = triggerElement;
  var expenses_id = $(current_selected_variant).data("id");
  $('input[name="expenses_id"]').val(id);
  var note = $(current_selected_variant).data("note");
  $('input[name="note"]').val(note);
  var amount = $(current_selected_variant).data("amount");
  $('input[name="amount"]').val(amount);
  var expenses_type = $(current_selected_variant).data("expenses_type");
  $('input[name="expenses_type"]').val(expenses_type);
  var expenses_date = $(current_selected_variant).data("expenses_date");
  $('input[name="expenses_date"]').val(expenses_date);
});

// report date filter
var start_date = "";
var end_date = "";
var payment_type_filter = "";

$("#payment_type_filter").on("change", function () {
  payment_type_filter = $(this).find("option:selected").val();
});

$(function () {
  $('input[name="daterange"]').daterangepicker(
    {
      opens: "left",
    },
    function (start, end) {
      start_date = start.format("YYYY-MM-DD");
      end_date = end.format("YYYY-MM-DD");
    }
  );
});

$("#clear").on("click", function () {
  start_date = "";
  end_date = "";
  $('input[name="daterange"]').val("Date Range Picker");
  console.log(start_date);
  console.log(end_date);
});

$("#date").on("change", function () {
  console.log(start_date);
  console.log(end_date);
});

function reports_query(p) {
  return {
    search: p.search,
    limit: p.limit,
    sort: p.sort,
    order: p.order,
    offset: p.offset,
    start_date: start_date,
    end_date: end_date,
    payment_type_filter: payment_type_filter,
  };
}
$("#apply").on("click", function (e) {
  $("#payment_reports_table").bootstrapTable("refresh");
});

// sales date filter

var start_date = "";
var end_date = "";
$("#payment_type_filter").on("change", function () {
  payment_type_filter = $(this).find("option:selected").val();
});
$(function () {
  $('input[name="daterange"]').daterangepicker(
    {
      opens: "left",
    },
    function (start, end) {
      start_date = start.format("YYYY-MM-DD");
      end_date = end.format("YYYY-MM-DD");
    }
  );
});

$("#date").on("change", function () {});

function reports_query(p) {
  return {
    search: p.search,
    limit: p.limit,
    sort: p.sort,
    order: p.order,
    offset: p.offset,
    start_date: start_date,
    end_date: end_date,
    payment_type_filter: payment_type_filter,
  };
}
$("#apply").on("click", function (e) {
  $("#sales_summary_table").bootstrapTable("refresh");
});

// profit loss filter
var start_date = "";
var end_date = "";

$(function () {
  $('input[name="daterange"]').daterangepicker(
    {
      opens: "left",
    },
    function (start, end) {
      start_date = start.format("YYYY-MM-DD");
      end_date = end.format("YYYY-MM-DD");
    }
  );
});

$("#date_profit_loss").on("change", function () {});

function pl_query(p) {
  return {
    search: p.search,
    limit: p.limit,
    sort: p.sort,
    order: p.order,
    offset: p.offset,
    start_date: start_date,
    end_date: end_date,
  };
}
$("#apply").on("click", function (e) {
  $("#profit_loss_table").bootstrapTable("refresh");
});

$("#clear").on("click", function () {
  start_date = "";
  end_date = "";
  $('input[name="date_range"]').val("Date Range Picker");
  console.log(start_date);
  console.log(end_date);
});

$("#general_setting_form").on("submit", function (e) {
  e.preventDefault();
  console.log(e);
  var formData = new FormData(this);
  formData.append(csrf_token, csrf_hash);
  $.ajax({
    type: "post",
    url: base_url + "/admin/settings/save_settings",
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
        iziToast.success({
          title: "Success!",
          message: result.message,
          position: "topRight",
        });
      }
    },
  });
});

$("#payment_gateway_setting_form").on("submit", function (e) {
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
        window.location = base_url + "/admin/settings/payment_gateway";
      }
    },
  });
});

// SMTP Email settings
$("#email_settings").on("submit", function (e) {
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
        Object.keys(result.message).map((key) => {
          iziToast.error({
            title: "Error!",
            message: result.message[key],
            position: "topRight",
          });
        });
      } else {
        console.log(result.message);

        Object.keys(result.message).map((key) => {
          iziToast.success({
            title: "Success",
            message: result.message,
            position: "topRight",
          });
        });
        setTimeout(() => {
          window.location = base_url + "/admin/settings/email";
        }, 2000);
      }
    },
  });
});

$("#about_us_setting_form").on("submit", function (e) {
  e.preventDefault();
  tinymce.triggerSave();
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
        window.location = base_url + "/admin/settings/about_us";
      }
    },
  });
});
$("#clear").on("click", function (e) {
  e.preventDefault();
  tinymce.activeEditor.setContent("");
});

//  Refund policy form settings
$("#refund_policy_setting_form").on("submit", function (e) {
  e.preventDefault();
  tinymce.triggerSave();
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
        window.location = base_url + "/admin/settings/refund_policy";
      }
    },
  });
});
$("#terms_and_conditions_setting_form").on("submit", function (e) {
  e.preventDefault();
  tinymce.triggerSave();
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
        window.location = base_url + "/admin/settings/terms_and_conditions";
      }
    },
  });
});
$("#privacy_policy_setting_form").on("submit", function (e) {
  e.preventDefault();
  tinymce.triggerSave();
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
        window.location = base_url + "/admin/settings/privacy_policy";
      }
    },
  });
});

$("#clear").on("click", function (e) {
  e.preventDefault();
  tinymce.activeEditor.setContent("");
});

$(document).ready(function () {
  tinymce.init({
    selector: "#about_us",
    height: "480",
  });
});
$(document).ready(function () {
  tinymce.init({
    selector: "#privacy_policy",
    height: "480",
  });
});
$(document).ready(function () {
  tinymce.init({
    selector: "#refund_policy",
    height: "480",
  });
});
$(document).ready(function () {
  tinymce.init({
    selector: "#terms_and_conditions",
    height: "480",
  });
});

$("#tax_form").on("submit", function (e) {
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
        iziToast.error({
          title: "Error!",
          message: result.message[key],
          position: "topRight",
        });
        setTimeout(() => {
          window.location = base_url + "/admin/tax";
        }, 3000);
      }
    },
  });
});
// dropzone

if (document.getElementById("system-update-dropzone")) {
  var systemDropzone = new Dropzone("#system-update-dropzone", {
    url: base_url + "/admin/updater/upload_update_file",
    paramName: "update_file",
    autoProcessQueue: false,
    parallelUploads: 1,
    maxFiles: 1,
    timeout: 360000,
    autoDiscover: false,
    addRemoveLinks: true,
    dictRemoveFile: "x",
    dictMaxFilesExceeded: "Only 1 file can be uploaded at a time ",
    dictResponseError: "Error",
    uploadMultiple: true,
    dictDefaultMessage:
      '<p><input type="button" value="Select Files" class="btn btn-success" /><br> or <br> Drag & Drop System Update / Installable / Plugin\'s .zip file Here</p>',
  });

  systemDropzone.on("addedfile", function (file) {
    var i = 0;
    if (this.files.length) {
      var _i, _len;
      for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) {
        if (
          this.files[_i].name === file.name &&
          this.files[_i].size === file.size &&
          this.files[_i].lastModifiedDate.toString() ===
            file.lastModifiedDate.toString()
        ) {
          this.removeFile(file);
          i++;
        }
      }
    }
  });

  systemDropzone.on("error", function (file, response) {
    console.log(response);
  });

  systemDropzone.on("sending", function (file, xhr, formData) {
    formData.append(csrf_token, csrf_hash);
    xhr.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        var response = JSON.parse(this.response);
        csrf_token = response.csrf_token;
        csrf_hash = response.csrf_hash;
        if (response["error"] == false) {
          showToastMessage(response["message"], "success");
        } else {
          showToastMessage(response["message"], "error");
        }
        $(file.previewElement).find(".dz-error-message").text(response.message);
      }
    };
  });
  $("#system_update_btn").on("click", function (e) {
    e.preventDefault();
    systemDropzone.processQueue();
  });
}

// top selling products
var start_date = "";
var end_date = "";

$(function () {
  $('input[name="daterange"]').daterangepicker(
    {
      opens: "left",
    },
    function (start, end) {
      start_date = start.format("YYYY-MM-DD");
      end_date = end.format("YYYY-MM-DD");
    }
  );
});

$("#date_top_selling_products").on("change", function () {});

function top_selling_products_query(p) {
  return {
    search: p.search,
    limit: p.limit,
    sort: p.sort,
    order: p.order,
    offset: p.offset,
    start_date: start_date,
    end_date: end_date,
  };
}
$("#apply").on("click", function (e) {
  $("#top_selling_products_table").bootstrapTable("refresh");
});

$("#clear").on("click", function () {
  start_date = "";
  end_date = "";
  $('input[name="date_range"]').val("Date Range Picker");
  console.log(start_date);
  console.log(end_date);
});

var start_date = "";
var end_date = "";

$(function () {
  $('input[name="daterange"]').daterangepicker(
    {
      opens: "left",
    },
    function (start, end) {
      start_date = start.format("YYYY-MM-DD");
      end_date = end.format("YYYY-MM-DD");
    }
  );
});

// best_customers
$("#date_best_customers").on("change", function () {});

function best_customers_query(p) {
  return {
    search: p.search,
    limit: p.limit,
    sort: p.sort,
    order: p.order,
    offset: p.offset,
    start_date: start_date,
    end_date: end_date,
  };
}
$("#apply").on("click", function (e) {
  $("#best_customers_table").bootstrapTable("refresh");
});

$("#clear").on("click", function () {
  start_date = "";
  end_date = "";
  $('input[name="date_range"]').val("Date Range Picker");
  console.log(start_date);
  console.log(end_date);
});

// purchases report

var start_date = "";
var end_date = "";
var payment_status_filter = "";
var supplier_id = "";

$("#payment_status_filter").on("change", function () {
  payment_status_filter = $(this).find("option:selected").val();
});

$("#supplier_filter").on("change", function () {
  supplier_id = $(this).find("option:selected").val();
});

$(function () {
  $('input[name="daterange"]').daterangepicker(
    {
      opens: "left",
    },
    function (start, end) {
      start_date = start.format("YYYY-MM-DD");
      end_date = end.format("YYYY-MM-DD");
    }
  );
});

$("#clear").on("click", function () {
  start_date = "";
  end_date = "";
  $('input[name="daterange"]').val("Date Range Picker");
  console.log(start_date);
  console.log(end_date);
});

$("#date_purchases_report").on("change", function () {
  console.log(start_date);
  console.log(end_date);
});

function purchase_report_query(p) {
  return {
    search: p.search,
    limit: p.limit,
    sort: p.sort,
    order: p.order,
    offset: p.offset,
    start_date: start_date,
    end_date: end_date,
    payment_status_filter: payment_status_filter,
    supplier_id: supplier_id,
  };
}
$("#apply").on("click", function (e) {
  $("#purchase_report_table").bootstrapTable("refresh");
});

// generate barcode

$("#generate-barcode").on("click", function (e) {
  $(".barcode").text("");
  $(".barcode").val("");
  e.preventDefault();

  var quantity = $("#quantity").val();
  var barcode_value = "";
  var barcode_name = "";

  if ($("#products_name option:selected").text() == "Select") {
    return false;
  } else {
    barcode_value = $("#products_name").val();
    barcode_name = $("#products_name option:selected").text();
  }
  console.log(barcode_value, barcode_name);
  var i = 0;
  var div = "";
  for (i = 0; i < quantity; i++) {
    div =
      '<svg id = "barcode" class="barcode border border-dark m-2 selection-to-print "  jsbarcode-format="auto" jsbarcode-value = "' +
      barcode_value +
      '" jsbarcode-textmargin="5"  jsbarcode-text = "' +
      barcode_value +
      '"jsbarcode-fontoptions="bold"id="barcode"></svg>';
    $("#bar-gn").append(div);
    document
      .getElementById("barcode-print")
      .addEventListener("click", function () {
        var printContents = document.getElementById("printDiv").innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
      });
  }
  if (i == 0) {
    iziToast.error({
      title: "Error",
      message: "please select the quantity",
      position: "topRight",
    });
  }
  JsBarcode(".barcode").init();
});

$("#barcode-reset").on("click", function (e) {
  Swal.fire({
    title: "Are you sure? ",
    text: "Want to clear Barcode!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, clear it!",
  }).then((result) => {
    if (result.value) {
      $("#bar-gn").empty();
      $("#quantity").val("");
      $("#products_name").val("");
    }
  });
});

// barcode_modal

// close barcode modal

$("#barcode_Modal").on("hidden.bs.modal", function (e) {
  $("#variant-barcode").empty();
});

function generate_barcode(product_id) {
  var error_msg = "Cannot Display Barcode";

  var data = {
    id: product_id,
  };

  $.ajax({
    type: "GET",
    url: site_url + "/admin/products/json",
    cache: false,
    data: data,
    dataType: "json",
    success: function (result) {
      if (result.error == true) {
        console.log(error_msg);
      } else {
        display_barcode(result.data);
      }
    },
  });
}
$(document).on(
  "show.bs.modal",
  "#barcode_Modal",
  function display_barcode() {}
);

function display_barcode(data) {
  if (data == undefined) {
    return;
  }
  var product_name = data[0]["name"];
  var variants = data[0]["variants"];
  var total_variants = variants.length;
  var i = 0;
  var div2 = "";

  for (i = 0; i < total_variants; i++) {
    console.log(variants[i]);

    let code = variants[i]["barcode"]?.trim() || variants[i]["id"];
    var variant_id = variants[i]["id"];
    var variant_name =
      variants[i]["barcode"]?.trim() || variants[i]["variant_name"];

    div2 =
      '<div class = "col-md-3 text-center"> ' +
      "<h6>" +
      product_name +
      " - " +
      variants[i]["variant_name"] +
      "  </h6>" +
      '<svg id = "barcode" class="barcode  border border-dark m-2 selection-to-print "  jsbarcode-format="auto"jsbarcode-value = "' +
      code +
      '" jsbarcode-textmargin="5"  jsbarcode-text = "' +
      variant_name +
      '"jsbarcode-fontoptions="bold"id="barcode"></svg></div>';

    $("#variant-barcode").append(div2);
  }
  document
    .getElementById("download-barcode")
    .addEventListener("click", function () {
      var printContents = document.getElementById("printDiv").innerHTML;
      var originalContents = document.body.innerHTML;
      document.body.innerHTML = printContents;
      window.print();
      document.body.innerHTML = originalContents;
      window.location.reload();
    });
  JsBarcode(".barcode").init();
}

// database backup

$("#backup_database").on("click", function (e) {
  Swal.fire({
    title: "Create Database Backup",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes",
  }).then((result) => {
    if (result.value) {
      var data = {
        ["csrf_hash"]: csrf_hash,
        ["csrf_token"]: csrf_token,
      };

      $.ajax({
        type: "POST",
        url: site_url + "admin/database/backup_database",
        data: data,
        dataType: "json",
        success: function (result) {
          csrf_token = result["csrf_token"];
          csrf_hash = result["csrf_hash"];
          if (result.error == false) {
            showToastMessage(result.message, "success");
            window.location.reload();
          } else {
            showToastMessage(result.message, "error");
          }
        },
      });
    }
  });
});

function delete_backup(e) {
  var file_name = e["id"];
  console.log(file_name);

  data = {
    file_name: file_name,
    ["csrf_hash"]: csrf_hash,
    ["csrf_token"]: csrf_token,
  };

  $.ajax({
    type: "POST",
    url: site_url + "/admin/database/delete",
    data: data,
    dataType: "json",
    success: function (result) {
      csrf_token = result["csrf_token"];
      csrf_hash = result["csrf_hash"];
      if (result.error == false) {
        showToastMessage(result.message, "success");
        $("#backup_table").bootstrapTable("refresh");
      } else {
        showToastMessage(result.message, "error");
      }
    },
  });
}

function mail_backup(e) {
  var file_name = e["id"];
  console.log(file_name);

  data = {
    file_name: file_name,
    ["csrf_hash"]: csrf_hash,
    ["csrf_token"]: csrf_token,
  };
  $("#file_id").val(file_name);
}
$("#mail_DBbackup").on("hidden.bs.modal", function (e) {
  $("#email-set").empty();
  $("#message").empty();
});

$("#mailDB").on("submit", function (e) {
  e.preventDefault();
  var formData = new FormData(this);

  formData.append(csrf_token, csrf_hash);

  $.ajax({
    type: "POST",
    url: site_url + "/admin/database/mail_database",
    data: formData,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
    success: function (result) {
      csrf_token = result["csrf_token"];
      csrf_hash = result["csrf_hash"];
      if (result.error == false) {
        showToastMessage(result.message, "success");
        $("#backup_table").bootstrapTable("refresh");
      } else {
        showToastMessage(result.message, "error");
        result.data.console;
      }
    },
  });
});

function download_backup(e) {
  Swal.fire({
    title: "Download Database Backup",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes",
  }).then((result) => {
    var file_name = e["id"];
    console.log(file_name);

    data = {
      file_name: file_name,
      ["csrf_hash"]: csrf_hash,
      ["csrf_token"]: csrf_token,
    };

    var uri = site_url + ("public/database_backup/" + file_name);
    var link = document.createElement("a");
    // If you don't know the name or want to use
    // the webserver default set name = ''
    link.setAttribute("download", "");
    link.href = uri;
    document.body.appendChild(link);
    link.click();
    link.remove();
    showToastMessage("Downloaded", "success");
  });
}

function scanned_barcode(e) {
  var barcode_value = "";
  barcode_value = e;
  var limit = $("input[name=limit]").val();
  var offset = $("input[name=offset]").val();
  var search = $("#search_product").val();
  var flag = null;
  console.log("ajax with " + barcode_value);

  $.ajax({
    type: "GET",
    url: site_url + "/admin/products/scanned_barcode_items",
    cache: false,
    data: {
      variant_id: barcode_value,
      search: search,
      limit: limit,
      offset: offset,
    },

    dataType: "json",
    success: function (result) {
      var data = result;

      if (result.error == true) {
        console.log(result.message);
        iziToast.error({
          title: "Error Occurred",
          message: result.message,
          position: "topRight",
        });
      } else {
        var products = result.data;
        if (products) {
          var product_id = products["id"];
          var tax_id = products["tax_id"];
          var business_id = products["business_id"];
          var is_tax_included = products["is_tax_included"];
          var product_variants = products["variants"][0];
          console.log(product_variants);
          var product_variant_id = product_variants["id"];
          var variant_name = product_variants["variant_name"];
          var product_name = products["name"];
          var image = site_url + products["image"];
          var price = product_variants["sale_price"];
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

          var session_business_id = $("#business_id").val();

          var cart = localStorage.getItem("cart" + session_business_id);
          cart =
            localStorage.getItem("cart" + session_business_id) !== null
              ? JSON.parse(cart)
              : null;
          if (cart !== null && cart !== undefined) {
            if (
              cart.find(
                (item) => item.product_variant_id === product_variant_id
              )
            ) {
              var message = "This item is already present in your cart";
              show_message("Oops!", message, "error");
              return;
            }
            message = "Adding item to cart";
            cart.push(cart_item);
          } else {
            cart = [cart_item];
          }
          localStorage.setItem("cart" + business_id, JSON.stringify(cart));
          display_cart();
          final_total();
        }
      }
    },
  });
}

let code = "";
let reading = false;
// var scanned_barcode = "";
document.addEventListener("keypress", (e) => {
  //usually scanners throw an 'Enter' key at the end of read
  if (e.keyCode == 13) {
    if (code.length >= 1) {
      console.log(code);
      //  console.log('scanned_barcode ' + code);

      /// code ready to use
      // code = "";
      scanned_barcode(code);
    }
  } else {
    code += e.key; //while this is not an 'enter' it stores the every key
  }

  //run a timeout of 200ms at the first read and clear everything
  if (!reading) {
    reading = true;
    setTimeout(() => {
      code = "";
      reading = false;
    }, 200); //200 works fine for me but you can adjust it
  }

  // alert(scanned_barcode);
});

var category_id = "";
$("#product_category").on("change", function () {
  category_id = $(this).find("option:selected").val();
  $("#products_table").bootstrapTable("refresh");
});
let filter_brand_id = "";
$(document).on("change", "#product_brand", function () {
  filter_brand_id = $(this).val();
  $("#products_table").bootstrapTable("refresh");
});
function cat_query(params) {
  console.log(params); // Debug: Check parameters
  return {
    category_id: category_id,
    brand_id: filter_brand_id,
    limit: params.limit,
    offset: params.offset,
    sort: params.sort,
    order: params.order,
    search: params.search,
  };
}

$(document).ready(function () {
  $(".select_product").select2();
});

//sales order

var product = [];
var qty;
var discount;
var price;
var count = 1;
if ($("#sales_order_form").length > 0) {
  $(document).on("change", ".search_products", function (e) {
    e.preventDefault();
    data = $(".search_products").select2("data")[0];
    var table_data = new Object();
    price = data.sale_price;
    table_data.name = data.name + "-" + data.variant_name;
    table_data.image =
      '<img src="' +
      site_url +
      data.image +
      '" width="60px"  class="img-fluid"/>';
    table_data.id = data.id;
    table_data.quantity =
      '<input type="number" class="form-control qty" value="1" min="0.00" step="0.1"  data-price ="' +
      price +
      '" name="qty[]" placeholder="Ex.1">';
    table_data.price = `
      <input 
        type="number" 
        class="form-control price" 
        value="${data.sale_price}"  step="0.01" 
        min="1" 
        name="price[]" 
        placeholder="Ex. 1" 
      >
     `;
    table_data.discount =
      '<input type="number" class="form-control discount" min="1" data-price ="' +
      price +
      '" name="discount[]" placeholder="Ex.1" step = "0.01">';
    table_data.total = '<strong class="table_price">' + price + "</strong>";

    var is_exist = false;

    $.each(product, function (i, e) {
      if (e.name === data.variant_name) {
        iziToast.error({
          message: `<span style="text-transform:capitalize">${data.variant_name} is already in list!</span> `,
        });
        is_exist = true;
        return false;
      }
    });

    if (is_exist === false) {
      product.push({
        variant_id: data.id,
        name: data.name,
        price: data.sale_price,
        product_id: data.product_id,
        // is_tax_included:data.is_tax_included,
        // product_name:data.name,
        variant_name: data.variant_name,
        // image:data.image,
        // tax_id:data.tax_id
      });

      $("#sale_product_id").val(JSON.stringify(product));
      product_details(this);
      $('input[name="products"]').val(JSON.stringify([]));
    }
    $("#sales_order").bootstrapTable("insertRow", {
      index: 0,
      row: table_data,
    });
    count++;
    purchase_total();
  });
}

//sales_order

$(document).on("submit", "#sales_order_form", function (e) {
  e.preventDefault();
  var formData = new FormData(this);
  console.log(formData);
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
        setTimeout(() => {
          showToastMessage(result.message, "success");
          location.href = base_url + "/admin/orders/orders";
        }, 1000);
      } else {
        Object.keys(result.message).map((key) => {
          showToastMessage(result["message"][key], "error");
        });
        return;
      }
    },
  });
});

$("#select_time_zone").on("change", function () {
  var mysql_timezone = $(this).find(":selected").data("gmt");
  $("#mysql_timezone").val(mysql_timezone);
  console.log($("#mysql_timezone").val());
});

// payment remainder
$("#filter").on("click", function (e) {
  $("#payment_reminder_table").bootstrapTable("refresh");
});

function payment_reminder(order_id) {
  var order_id = order_id;
  Swal.fire({
    title: "Send Reminder Message",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes",
  }).then((result) => {
    if (result.value == true) {
      $.ajax({
        type: "GET",
        url: site_url + "/admin/orders/send_reminder",
        data: {
          order_id: order_id,
        },
        dataType: "json",
        success: function (result) {
          csrf_token = result["csrf_token"];
          csrf_hash = result["csrf_hash"];
          if (result.error == false) {
            showToastMessage(result.message, "success");
          } else {
            showToastMessage(result.message, "error");
          }
        },
      });
    }
  });
}

// Quantity Alert Message
// Quantity Alert Message - Updated to handle both product and variant level stock
$(document).ready(function () {
  // Check if iziToast is loaded (required for notifications)
  if (typeof iziToast === "undefined") {
    console.error("iziToast not loaded - stock alerts disabled");
    return;
  }

  // Configure default iziToast settings
  iziToast.settings({
    position: "topRight",
    timeout: 15000,
    closeOnClick: true,
    displayMode: "replace",
    transitionIn: "fadeInDown",
    transitionOut: "fadeOutUp",
  });

  // Function to show stock alert notification
  function showStockAlert(
    productName,
    variantName,
    currentStock,
    alertLevel,
    productId,
    variantId,
    isVariantLevel
  ) {
    // Create storage key to remember dismissed alerts
    const storageKey =
      "stockAlert_" + productId + (isVariantLevel ? "_" + variantId : "");

    // Check if user dismissed this alert previously
    if (sessionStorage.getItem(storageKey)) {
      return;
    }

    // Build the notification message
    const itemName = variantName
      ? `${productName} (${variantName})`
      : productName;
    const message = `<strong>${itemName}</strong> is low on stock<br>
                        Current: ${currentStock} | Alert Level: ${alertLevel}`;

    // Show the notification
    iziToast.warning({
      title: "LOW STOCK WARNING",
      message: message,
      icon: "fas fa-exclamation-triangle",
      buttons: [
        [
          '<button><i class="fas fa-shopping-cart"></i> Order Now</button>',
          function (instance, toast) {
            window.location.href =
              site_url + "admin/purchases/purchase_orders/order";
            instance.hide(toast);
          },
        ],
        [
          '<button><i class="fas fa-times"></i> Dismiss</button>',
          function (instance, toast) {
            instance.hide(toast);
          },
        ],
        [
          '<button><i class="fas fa-eye-slash"></i> Don\'t Show Again</button>',
          function (instance, toast) {
            sessionStorage.setItem(storageKey, "true");
            instance.hide(toast);
          },
        ],
      ],
      onClosing: function () {
        // Optional: Add any cleanup here
      },
    });
  }

  // Load stock alerts after page fully loads
  setTimeout(function () {
    $.ajax({
      url: site_url + "admin/products/stock_alert",
      type: "GET",
      dataType: "json",
      success: function (response) {
        if (response && response.rows && response.rows.length > 0) {
          response.rows.forEach(function (item) {
            try {
              // Parse stock values
              const currentStock = parseFloat(item.stock) || 0;
              const alertLevel = parseFloat(item.qty_alert) || 0;

              // Check if we should show alert
              if (alertLevel > 0 && currentStock <= alertLevel) {
                const isVariantLevel = item.stock_management_type == 2;
                showStockAlert(
                  item.product || "Unknown Product",
                  item.variant_name,
                  currentStock,
                  alertLevel,
                  item.product_id,
                  item.variant_id,
                  isVariantLevel
                );
              }
            } catch (e) {
              console.error("Error processing stock alert:", e);
            }
          });
        }
      },
      error: function (xhr, status, error) {
        console.error("Failed to load stock alerts:", error);
        // Optional: Show error notification
        iziToast.error({
          title: "Error",
          message: "Could not check stock levels",
          position: "topRight",
        });
      },
    });
  }, 2000); // 2-second delay to ensure everything is loaded
});
$(document).ready(function () {
  $(".phone-number").on("input", function () {
    let text = $(this).val();
    let pattern = /[^0-9]/;
    let cleanedText = text.replace(pattern, "");
    // Update the input field with the cleaned text
    $(this).val(cleanedText);

    // If the original text had non-numeric characters, show the error message
    if (pattern.test(text)) {
      $(".phone-number-error-message").text("Characters are not allowed");
      $(".phone-number-error-message").show();
    } else {
      $(".phone-number-error-message").text("");
      $(".phone-number-error-message").hide();
    }
  });
  $("#credit_period").on("input", function () {
    let text = $(this).val();
    let pattern = /[^0-9]/;
    let cleanedText = text.replace(pattern, "");
    // Update the input field with the cleaned text
    $(this).val(cleanedText);

    // If the original text had non-numeric characters, show the error message
    if (pattern.test(text)) {
      $(".credit_period-error-message").text("Characters are not allowed");
      $(".credit_period-error-message").show();
    } else {
      $(".credit_period-error-message").text("");
      $(".credit_period-error-message").hide();
    }
  });
  $("#credit_limit").on("input", function () {
    let text = $(this).val();
    let pattern = /[^0-9]/;
    let cleanedText = text.replace(pattern, "");
    // Update the input field with the cleaned text
    $(this).val(cleanedText);

    // If the original text had non-numeric characters, show the error message
    if (pattern.test(text)) {
      $(".credit_limit-error-message").text("Characters are not allowed");
      $(".credit_limit-error-message").show();
    } else {
      $(".credit_limit-error-message").text("");
      $(".credit_limit-error-message").hide();
    }
  });
  $("#supplier_form").on("submit", function (event) {
    let identity = $(".phone-number").val();
    let creditPeriod = $("#credit_period").val();
    let creditLimit = $("#credit_limit").val();
    let pattern = /[^0-9]/;

    let isInvalid = false;

    if (pattern.test(identity)) {
      isInvalid = true;
    }
    if (pattern.test(creditPeriod)) {
      isInvalid = true;
    }
    if (pattern.test(creditLimit)) {
      isInvalid = true;
    }

    if (isInvalid) {
      event.preventDefault();
      alert("Please ensure all fields contain only numeric values.");
    }
  });

  $("#conversion").on("input", function () {
    let value = $(this).val();
    if (value.length > 0) {
      if (value <= 0) {
        $(this).val("");
        iziToast.error({
          title: "Error",
          message: "Conversion must be greater than zero (0)",
          position: "topRight",
        });
      }
    }
  });

  $("#quantity").on("input", function () {
    let value = $(this).val();
    if (value.length > 0) {
      if (value <= 0) {
        $(this).val("");
        iziToast.error({
          title: "Error",
          message: "quantity must be greater than zero (0)",
          position: "topRight",
        });
      }
    }
  });

  $('input[name="identity"]').on("input", function () {
    let value = $(this).val();
    let pattern = /^[0-9]*$/;

    if (!pattern.test(value)) {
      $(this).val(value.replace(/[^0-9]/g, ""));
      iziToast.error({
        title: "Error",
        message: "Only numbers are allowed !",
        position: "topRight",
      });
    }
  });

  $('input[name="amount"]').on("input", function () {
    let value = $(this).val();
    let pattern = /^[0-9]*\.?[0-9]+$/;

    if (!pattern.test(value)) {
      $(this).val(value.replace(/[^0-9]/g, ""));
      iziToast.error({
        title: "Error",
        message: "Only numbers are allowed !",
        position: "topRight",
      });
    }
  });

  $('input[name="balance"]').on("input", function () {
    let value = $(this).val();
    let pattern = /^[0-9]*\.?[0-9]+$/;

    if (!pattern.test(value)) {
      $(this).val(value.replace(/[^0-9]/g, ""));
      iziToast.error({
        title: "Error",
        message: "Only numbers are allowed !",
        position: "topRight",
      });
    }
  });
});

document
  .getElementById("calculatorIcon")
  .addEventListener("click", function () {
    const dropdown = document.getElementById("dropdownCalculator");
    dropdown.style.display =
      dropdown.style.display === "block" ? "none" : "block";
  });

// Enable editing on double-click and restrict input to numbers and operators
document.getElementById("display").addEventListener("dblclick", function () {
  this.removeAttribute("readonly");
  this.focus();
});

document.getElementById("display").addEventListener("blur", function () {
  this.setAttribute("readonly", true);
});

// Restrict input to numbers, operators, and control keys (backspace, enter)
document
  .getElementById("display")
  .addEventListener("keydown", function (event) {
    const allowedKeys = [
      "0",
      "1",
      "2",
      "3",
      "4",
      "5",
      "6",
      "7",
      "8",
      "9",
      "+",
      "-",
      "*",
      "/",
      ".",
      "(",
      ")",
      "Backspace",
      "Enter",
    ];
    if (
      !allowedKeys.includes(event.key) &&
      !event.ctrlKey &&
      event.key !== "ArrowLeft" &&
      event.key !== "ArrowRight"
    ) {
      event.preventDefault();
    }

    // Calculate result on Enter key press
    if (event.key === "Enter") {
      event.preventDefault();
      calculateResult();
      this.setAttribute("readonly", true);
    }
    if (event.key === "Delete") {
      this.value = ""; // Clear the display
    }
  });

// Calculator functions
function appendValue(value) {
  document.getElementById("display").value += value;
}

function clearDisplay() {
  document.getElementById("display").value = "";
}

function calculateResult() {
  const display = document.getElementById("display");
  try {
    display.value = eval(display.value); // Evaluate the expression
  } catch (error) {
    display.value = "Error"; // If there's an error in the expression
  }
}

// Backspace function to remove the last character
function backspace() {
  const display = document.getElementById("display");
  display.value = display.value.slice(0, -1); // Remove the last character
}

// Prevent the calculator from closing when clicking inside the calculator area
document
  .getElementById("dropdownCalculator")
  .addEventListener("click", function (event) {
    event.stopPropagation(); // Prevent event bubbling
  });

// Close the dropdown when clicking outside the calculator or the icon
document.addEventListener("click", function (event) {
  const dropdown = document.getElementById("dropdownCalculator");
  const calculatorIcon = document.getElementById("calculatorIcon");
  if (
    !calculatorIcon.contains(event.target) &&
    !dropdown.contains(event.target)
  ) {
    dropdown.style.display = "none";
  }
});

$(document).ready(function () {
  if ($("#is_tax_inlcuded").length > 0) {
    if ($("#is_tax_inlcuded").prop("checked")) {
      // if tax is included in price
      $("#tax_rows").addClass("d-none");
    } else {
      // if tax is excluded in price
      $("#tax_rows").removeClass("d-none");
    }

    $("#is_tax_inlcuded").on("change", function () {
      let ischaked = $(this).prop("checked");

      if (ischaked) {
        // if tax is included in price
        $("#tax_rows").addClass("d-none");
      } else {
        // if tax is excluded in price
        $("#tax_rows").removeClass("d-none");
      }
    });
  }
});

$(document).ready(function () {
  if (document.getElementById("tax_ids")) {
    $.ajax({
      type: "POST",
      url: site_url + "admin/tax/get_taxs",
      success: function (response) {
        let taxArray = response.taxs;
        taxArray = taxArray.map((tax) => {
          return { value: tax.name, id: tax.id };
        });

        let inputElm = document.getElementById("tax_ids");

        if (document.getElementById("product_id").value.length > 0) {
          let products_tax_value =
            document.getElementById("products_tax_value").value;
          inputElm.value = products_tax_value;
          products_tax_value = JSON.parse(products_tax_value);
          let tagify = new Tagify(inputElm, {
            enforceWhitelist: true,
            whitelist: taxArray,
            tagTextProp: "name",
            userInput: false,
            dropdown: {
              closeOnSelect: false,
              enabled: 0,
            },
            value: taxArray,
          });
        } else {
          let tagify = new Tagify(inputElm, {
            enforceWhitelist: true,
            whitelist: taxArray,
            tagTextProp: "name",
            userInput: false,
            dropdown: {
              closeOnSelect: false,
              enabled: 0,
            },
          });
        }
      },
    });
  }

  if (document.getElementById("service_form")) {
    $.ajax({
      type: "POST",
      url: site_url + "admin/tax/get_taxs",
      success: function (response) {
        let taxArray = response.taxs;
        taxArray = taxArray.map((tax) => {
          return { value: tax.name, id: tax.id, percentage: tax.percentage };
        });

        let inputElm = document.getElementById("service_taxes");

        if (document.getElementById("service_id").value.length > 0) {
          let service_taxes_values = document.getElementById(
            "service_taxes_values"
          ).value;
          inputElm.value = service_taxes_values;
          service_taxes_values = JSON.parse(service_taxes_values);
          let tagify = new Tagify(inputElm, {
            enforceWhitelist: true,
            whitelist: taxArray,
            tagTextProp: "name",
            userInput: false,
            dropdown: {
              closeOnSelect: false,
              enabled: 0,
            },
            value: taxArray,
          });
        } else {
          let tagify = new Tagify(inputElm, {
            enforceWhitelist: true,
            whitelist: taxArray,
            tagTextProp: "name",
            userInput: false,
            dropdown: {
              closeOnSelect: false,
              enabled: 0,
            },
          });
        }
      },
    });
  }
});

$(document).ready(function () {
  $("#storeWarehouse").on("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);

    $.ajax({
      type: "post",
      url: this.action,
      data: formData,
      cache: false,
      processData: false,
      contentType: false,
      success: function (result) {
        csrf_token = result["csrf_token"];
        csrf_hash = result["csrf_hash"];
        if (result.error == true) {
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
          $("#createWarehouseModel").modal("hide");
          setTimeout(() => {
            window.location.href = window.location.href;
          }, 3000);
        }
      },
    });
  });
  $("#editWarehouse").on("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);

    $.ajax({
      type: "post",
      url: this.action,
      data: formData,
      cache: false,
      processData: false,
      contentType: false,
      success: function (result) {
        csrf_token = result["csrf_token"];
        csrf_hash = result["csrf_hash"];
        if (result.error == true) {
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
          $("#editWarehouseModel").modal("hide");
          setTimeout(() => {
            window.location.href = window.location.href;
          }, 3000);
        }
      },
    });
  });

  $("#warehouse_qty_alert").on("input", function (e) {
    const value = parseInt($(this).val());

    if (value < 0) {
      iziToast.error({
        title: "Error",
        message: "Negative values are not allowed",
        position: "topRight",
      });

      $(this).val("");
    }
  });
  $(".No-negative").on("input", function (e) {
    const value = parseInt($(this).val());

    if (value < 0) {
      iziToast.error({
        title: "Error",
        message: "Negative values are not allowed",
        position: "topRight",
      });

      $(this).val("");
    }
  });

  $("#simple_product_qty_alert").on("input", function (e) {
    const value = parseInt($(this).val());
    if (value < 0) {
      iziToast.error({
        title: "Error",
        message: "Negative values are not allowed",
        position: "topRight",
      });
      $(this).val("");
    }
  });
  $("#simple_product_stock").on("input", function (e) {
    const value = parseInt($(this).val());
    if (value < 0) {
      iziToast.error({
        title: "Error",
        message: "Negative values are not allowed",
        position: "topRight",
      });
      $(this).val("");
    }
  });
});

function editWarehouse(id, url) {
  let formData = new FormData();
  formData.append(csrf_token, csrf_hash);
  formData.append("id", id);

  $.ajax({
    type: "post",
    url: `${url}${id}`,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (result) {
      csrf_token = result["csrf_token"];
      csrf_hash = result["csrf_hash"];
      if (result.error == true) {
        Object.keys(result.message).map((key) => {
          iziToast.error({
            title: "Error!",
            message: result.message[key],
            position: "topRight",
          });
        });
      } else {
        let data = result.data;
        $("#editWarehouseId").val(id);
        $("#editWarehouseName").val(data.name);
        $("#editWarehouseCountry").val(data.country);
        $("#editWarehouseCity").val(data.city);
        $("#editWarehouseZip_code").val(data.zip_code);
        $("#editWarehouseAddress").val(data.address);

        $("#editWarehouseModel").modal("show");
      }
    },
  });
}

$(document).on("click", ".addWarehouseBtn", function (e) {
  let mutli_lang_remove_warehouse = $("#mutli_lang_remove_warehouse").val();

  let variant_index = $(this).data("variant_index");

  let all_warehouses = $("#all_warehouses").val();
  if (all_warehouses) {
    all_warehouses = JSON.parse(all_warehouses);
    var warehouse_options = "<option value=''>Select Warehouse</option>";
    $.each(all_warehouses, function (i, warehouse) {
      warehouse_options +=
        '<option value = "' +
        warehouse["id"] +
        '" > ' +
        warehouse["name"] +
        "</option>";
    });
  }

  let warehouseRowHTML = `
                <div class="row">
                    <div class="col-md-3">
                        <div class="">
                            <label for="warehouse_id">Warehouse</label><span class="asterisk text-danger">*</span>
                            <select class="form-control" id="warehouse_id" name="warehouses[${variant_index}][warehouse_ids][]">
                                ${warehouse_options}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="">
                            <label for="warehouse_stock">Warehouse Stock</label><span class="asterisk text-danger">*</span>
                            <input type="number" class="form-control No-negative" id="warehouse_stock" name="warehouses[${variant_index}][warehouse_stock][]">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="">
                            <label for="warehouse_qty_alert">Warehouse Minimum stock level</label><span class="asterisk text-danger">*</span>
                            <input type="number" class="form-control No-negative" id="warehouse_qty_alert" name="warehouses[${variant_index}][warehouse_qty_alert][]">
                        </div>
                    </div>
                    <div class="col-md-2 custom-col">
                        <label for="" class="d-block">${mutli_lang_remove_warehouse}</label>
                        <button class="btn btn-icon btn-danger remove-warehouse" type="button" data-variant_id="<?= $variant['id'] ?>" name="remove_warehouse" data-toggle="tooltip" data-placement="bottom" title="Remove warehouse"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;

  // $(".warehouses").append(warehouseRowHTML);
  $(this)
    .parent()
    .parent()
    .parent()
    .siblings(".warehouses")
    .append(warehouseRowHTML);
});

$(document).on("click", ".remove-warehouse", function (e) {
  e.preventDefault();
  $(this).parent().parent().remove();
});

function t_query(params) {
  return {
    search: params.search,
    sort: params.sort,
    order: params.order,
    offset: params.offset,
    limit: params.limit,
  };
}
$(document).ready(function () {
  $("#syncAllProductToWarehouse").on("submit", function (event) {
    event.preventDefault();
    let formdata = new FormData();

    formdata.append("warehouse_id", $("#warehouse_id").val());
    let url = $(this).data("action");
    $.ajax({
      type: "POST",
      url: url,
      data: formdata,
      cache: false,
      processData: false,
      contentType: false,
      success: function (response) {
        let isError = response.error;
        if (isError) {
          iziToast.error({
            title: "Error",
            message: response.message,
            position: "topRight",
          });
        } else {
          iziToast.success({
            title: "Success",
            message: response.message,
            position: "topRight",
          });
        }
      },
    });
  });
});

$(document).ready(function () {
  $("#expenses_type").select2({
    placeholder: "Search for an Expense Type",
    dropdownParent: $("#expenses_modal"),
    ajax: {
      url: site_url + "admin/get_expenses_type",
      dataType: "json",
      delay: 250, // Add delay to prevent flooding requests
      data: function (params) {
        return {
          search: params.term, // Search term
        };
      },
      processResults: function (response) {
        return {
          results: $.map(response.data, function (item) {
            return {
              id: item.id,
              text: item.title,
              additionalData: {
                expense_type_id: item.product_variant_id,
              },
            };
          }),
        };
      },
      cache: true,
    },
  });
});

function printInvoice() {
  let id = $("#pos_quick_invoice").data("id");
  var printWindow = window.open(
    base_url + "/admin/invoices/thermal_print/" + id,
    "_blank"
  );
  printWindow.onload = function () {
    printWindow.print();
  };
}

$(document).on("click", "#chat-scrn", function () {
  var data = $(this).data("value");
  $("nav.navbar.navbar-expand-lg.main-navbar").toggleClass("d-none");
  $("#sidebar-wrapper").parent().toggleClass("d-none");
  if (data == "max") {
    $(".main-footer").removeClass("chat-hide-show");
    $(".chat-full-screen")
      .addClass("main-content")
      .removeClass("chat-full-screen");
    $("#navebar-bg").addClass("navbar-bg");
    $(this).data("value", "min");
    $(this)
      .children()
      .removeClass("fas chat-scrn fa-compress")
      .addClass("fas chat-scrn fa-expand");
  } else {
    $("#navebar-bg").removeClass("navbar-bg");
    $(".main-footer").addClass("chat-hide-show");
    $(this)
      .children()
      .removeClass("fas chat-scrn fa-expand")
      .addClass("fas chat-scrn fa-compress");
    $(".main-content").addClass("chat-full-screen").removeClass("main-content");
    $(this).data("value", "max");
  }
});
function get_todays_stats() {
  let total_sale;
  let total_purchase;
  let total_expens;

  function fetchAndSetValue(elementId, url, dataKey, callback = null) {
    let el = document.getElementById(elementId);
    if (el) {
      $.ajax({
        type: "get",
        url: base_url + url,
        success: function (response) {
          let value = 0.0;
          let res = JSON.parse(response);
          if (!res.is_error) {
            value = res.data[dataKey];
          }
          let currency = $("#" + elementId).data("currency");
          $("#" + elementId).text(currency + " " + value);

          if (callback) callback(value);
        },
      });
    }
  }

  fetchAndSetValue(
    "today_sales",
    "/admin/todays_total_sales",
    "total_amount",
    (val) => (total_sale = val)
  );
  fetchAndSetValue(
    "today_purchase",
    "/admin/todays_total_purchase",
    "total_amount",
    (val) => (total_purchase = val)
  );
  fetchAndSetValue(
    "today_expanse",
    "/admin/get_todays_expense",
    "total_amount",
    (val) => (total_expens = val)
  );
  fetchAndSetValue(
    "today_payments",
    "/admin/todays_total_payment_resived",
    "total_amount"
  );
  fetchAndSetValue(
    "today_payments_remaining",
    "/admin/todays_total_payment_remaining",
    "diffrence"
  );
  fetchAndSetValue("today_paid", "/admin/todays_total_paids", "total_amount");
  fetchAndSetValue(
    "today_amount_to_pay",
    "/admin/todays_total_remaining",
    "diffrence"
  );
  fetchAndSetValue("today_profit", "/admin/totdays_profit", "profit");
}

$(document).ready(function () {
  get_todays_stats();
});
$(document).on("submit", "#add_brand", function (e) {
  e.preventDefault();
  let formdata = new FormData(this);
  let method = $(this).attr("method");
  let url = $(this).attr("action");

  $.ajax({
    type: method,
    url: url,
    data: formdata,
    cache: false,
    processData: false,
    contentType: false,
    success: function (response) {
      console.log(response);

      let isError = response.error;
      if (isError) {
        iziToast.error({
          title: "Error",
          message: response.message,
          position: "topRight",
        });
      } else {
        iziToast.success({
          title: "Success",
          message: response.message,
          position: "topRight",
        });
      }

      $("#addBrandModal").modal("hide");
      $("#brand_table").bootstrapTable("refresh");
    },
  });
});

function editBrand(id, route) {
  let formData = new FormData();
  formData.append(csrf_token, csrf_hash);
  formData.append("id", id);

  $.ajax({
    type: "post",
    url: `${route}`,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (result) {
      csrf_token = result["csrf_token"];
      csrf_hash = result["csrf_hash"];
      if (result.error == true) {
        Object.keys(result.message).map((key) => {
          iziToast.error({
            title: "Error!",
            message: result.message[key],
            position: "topRight",
          });
        });
      } else {
        let data = result.data[0];

        $("#brand_id").val(id);
        $("#edit_brand_name").val(data.name);
        $("#edit_brand_description").val(data.description);
        $("#editBrandModal").modal("show");
      }
    },
  });
}

$(document).on("submit", "#update_brand", function (e) {
  e.preventDefault();
  let formdata = new FormData(this);
  let method = $(this).attr("method");
  let url = $(this).attr("action");

  $.ajax({
    type: method,
    url: url,
    data: formdata,
    cache: false,
    processData: false,
    contentType: false,
    success: function (response) {
      console.log(response);

      let isError = response.error;
      if (isError) {
        iziToast.error({
          title: "Error",
          message: response.message,
          position: "topRight",
        });
      } else {
        iziToast.success({
          title: "Success",
          message: response.message,
          position: "topRight",
        });
      }
      $("#editBrandModal").modal("hide");
      $("#brand_table").bootstrapTable("refresh");
    },
  });
});
function deleteBrand(id, route) {
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    let formData = new FormData();
    formData.append("id", id);

    $.ajax({
      type: "post",
      url: `${route}`,
      data: formData,
      cache: false,
      processData: false,
      contentType: false,
      success: function (result) {
        csrf_token = result["csrf_token"];
        csrf_hash = result["csrf_hash"];
        if (result.error == true) {
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
              title: "Success !",
              message: result.message[key],
              position: "topRight",
            });
          });
        }
        $("#brand_table").bootstrapTable("refresh");
      },
    });
  });
}

// codes for the draft and hold bottuns
// Add these at the top of test.js
$(document).ready(function () {
  console.log("Document ready - initializing cart buttons");
  initCartButtons();
  updateDraftCount(); // Initialize count on page load
});
function initCartButtons() {
  // Update button initialization
  $(document)
    .off("click", "#hold_cart_btn")
    .on("click", "#hold_cart_btn", function (e) {
      e.preventDefault();
      holdCurrentCart();
    });

  $(document)
    .off("click", "#load_drafts_btn")
    .on("click", "#load_drafts_btn", function (e) {
      e.preventDefault();
      showDraftsModal();
    });
}

// Add this function
function updateDraftCount() {
  try {
    const business_id = $("#business_id").val();
    const drafts =
      JSON.parse(localStorage.getItem(`drafts${business_id}`)) || [];
    const count = drafts.length;
    $("#load_drafts_btn").html(`Load Drafts (${count})`);
  } catch (error) {
    console.error("Draft count error:", error);
  }
}

// Modified hold function
function holdCurrentCart() {
  try {
    const business_id = $("#business_id").val();
    const cart = localStorage.getItem(`cart${business_id}`);

    if (cart && JSON.parse(cart).length > 0) {
      const drafts =
        JSON.parse(localStorage.getItem(`drafts${business_id}`)) || [];
      const newDraft = {
        id: Date.now(),
        cart: JSON.parse(cart),
        created_at: new Date().toLocaleString(),
      };

      drafts.push(newDraft);
      localStorage.setItem(`drafts${business_id}`, JSON.stringify(drafts));
      localStorage.removeItem(`cart${business_id}`);

      display_cart();
      updateDraftCount();
      show_message("Success", "Cart saved as draft", "success");
      return;
    }
    show_message("Error", "No items to save", "error");
  } catch (error) {
    console.error("Hold error:", error);
    show_message("Error", error.message, "error");
  }
}

// Modified load function
function loadDraft(draftId) {
  try {
    const business_id = $("#business_id").val();
    let drafts = JSON.parse(localStorage.getItem(`drafts${business_id}`)) || [];
    const draftIndex = drafts.findIndex((d) => d.id === draftId);

    if (draftIndex !== -1) {
      localStorage.setItem(
        `cart${business_id}`,
        JSON.stringify(drafts[draftIndex].cart)
      );
      drafts.splice(draftIndex, 1);
      localStorage.setItem(`drafts${business_id}`, JSON.stringify(drafts));

      display_cart();
      updateDraftCount();
      $("#draftsModal").modal("hide");
      show_message("Success", "Draft loaded and removed", "success");
    }
  } catch (error) {
    console.error("Load error:", error);
    show_message("Error", error.message, "error");
  }
}

// Modified delete function
function deleteDraft(draftId) {
  if (confirm("Delete this draft permanently?")) {
    try {
      const business_id = $("#business_id").val();
      let drafts = JSON.parse(
        localStorage.getItem(`drafts${business_id}`) || "[]"
      );
      drafts = drafts.filter((d) => d.id !== draftId);
      localStorage.setItem(`drafts${business_id}`, JSON.stringify(drafts));

      // Update UI
      display_cart();
      updateDraftCount();
      $("#draftsModal").modal("hide");
      show_message("Success", "Draft loaded and removed", "success");
    } catch (error) {
      console.error("Delete error:", error);
      show_message("Error", error.message, "error");
    }
  }
}

// Modified modal function
function showDraftsModal() {
  try {
    const business_id = $("#business_id").val();
    const drafts =
      JSON.parse(localStorage.getItem(`drafts${business_id}`)) || [];

    let modalHTML = `
            <div class="modal fade" id="draftsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Saved Drafts</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ${
                              drafts.length
                                ? ""
                                : "<p>No saved drafts found</p>"
                            }
                            <div class="list-group">`;

    drafts.forEach((draft) => {
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
                </div>`;
    });

    modalHTML += `
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>`;

    $("#draftsModal").remove();
    $("body").append(modalHTML);

    const modal = new bootstrap.Modal(document.getElementById("draftsModal"));
    modal.show();
  } catch (error) {
    console.error("Modal error:", error);
    show_message("Error", error.message, "error");
  }
}
