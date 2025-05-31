<?php

use App\Models\Categories_model;

function getActiveCategories($user_id, $business_id)
{
    $category_model = new Categories_model();
    $categories_set = $category_model->get_categories($user_id, $business_id);
    $categories = [];

    foreach ($categories_set as $key) {
        if ($key['status']) {
            $categories[] = [
                'id' => $key['id'],
                "parent_id" => $key['parent_id'],
                "vendor_id" => $key['vendor_id'],
                "business_id" => $key['business_id'],
                'name' => $key['name'],
                'updated_at' => $key['updated_at'],
                'created_at' => $key['created_at'],
            ];
        }
    }

    return $categories;
}

function prepareOrdersItemsRow(array $ordersItems): array
{
    $img = '<div class="image-box-100 "><a href="' . base_url($ordersItems['image'])  . '" data-lightbox="image-1">
             <img src="' . base_url($ordersItems['image']) . '" class="image-100 image-box-100 img-fluid" />
            </a></div>';

    $rowData = htmlspecialchars(json_encode($ordersItems), ENT_QUOTES, 'UTF-8'); // Convert the entire row to JSON and escape it for HTML

    $edit_customer = "<button class='btn btn-primary btn-sm edit-order-item' data-row='{$rowData}' data-bs-toggle='modal' data-bs-target='#orders_items_edit_moadl'>
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
        'status_id'=> $ordersItems['status_id'],
        'actions' => $edit_customer,

    ];

    return $result;
}
