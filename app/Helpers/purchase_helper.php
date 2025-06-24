<?php

function getPurchaseValidationRules($request)
{
    $rules = [
        'purchase_date' => ['rules' => 'required', 'label' => 'Purchase Date'],
        'supplier_id'   => ['rules' => 'required', 'label' => 'Supplier'],
        'products'      => ['rules' => 'required', 'label' => 'Products'],
        'status'        => ['rules' => 'required', 'label' => 'Status'],
        'payment_status' => ['rules' => 'required', 'label' => 'Payment Status'],
        'warehouse_id'  => ['rules' => 'required', 'label' => 'Warehouse'],
    ];

     //Optionally, require at least one payment for partially_paid
     $payment_status = $request->getVar('payment_status');
     if ($payment_status == "partially_paid") {
         $payments = $request->getVar('payments');
         if (empty($payments) || !is_array($payments) || count($payments) == 0) {
             $rules['payments'] = ['rules' => 'required', 'label' => 'Payments'];
         }
     }

    return $rules;
}

function prepareOrdersItemsRow(array $ordersItems): array
{
    $img = '<div class="image-box-100 "><a href="' . base_url($ordersItems['image'])  . '" data-lightbox="image-1">
             <img src="' . base_url($ordersItems['image']) . '" class="image-100 image-box-100 img-fluid" />
            </a></div>';

    $rowData = htmlspecialchars(json_encode($ordersItems), ENT_QUOTES, 'UTF-8'); // Convert the entire row to JSON and escape it for HTML

    $actions_buttons = "<button class='btn btn-primary btn-sm edit-order-item' data-row='{$rowData}' data-bs-toggle='modal' data-bs-target='#orders_items_edit_moadl'>
        <i class='bi bi-pen'></i>
    </button>";

    $result = [
        'id' => $ordersItems['orders_items_id'],
        'order_id' => $ordersItems['order_id'],
        'categorey' => $ordersItems['category'] ?? '-',
        'brand' => $ordersItems['brand'] ?? "-",
        'image' => $img ?? "-",
        'warehouse_name' => $ordersItems['warehouse_name'],
        'product_name' => $ordersItems['product_name'],
        'quantity' => $ordersItems['quantity'],
        'price' => currency_location(decimal_points($ordersItems['price'])),
        'total' => currency_location(decimal_points($ordersItems['price']) *  $ordersItems['quantity']),
        'returned_quantity' => $ordersItems['returned_quantity'],
        'total_after_returned' => currency_location(decimal_points($ordersItems['price']) *  ($ordersItems['quantity'] - $ordersItems['returned_quantity'])),
        'status' => $ordersItems['status'],
        'status_id' => $ordersItems['status_id'],
        'actions' => $actions_buttons,
    ];

    return $result;
}