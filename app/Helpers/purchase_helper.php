<?php

function getUserId()
{
    $ionAuth = \Config\Services::ionAuth();
    $user_id = $_SESSION['user_id'];
    return $ionAuth->isTeamMember() ? get_vendor_for_teamMember($user_id) : $user_id;
}

function prepareBatchNumber($id, $expire_date)
{
    $current_timestamp = time(); // Unix timestamp
    if (!empty($expire_date)) {
        $expire_date = date('Y-m-d', strtotime($expire_date));
    } else {
        $expire_date = '0000-00-00'; // Default value if no expiry date is provided
    }

    $batch_number = "BATCH-{$id}-{$current_timestamp}-{$expire_date}";
    return $batch_number;
}

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

    $payment_status = $request->getVar('payment_status');

    if ($payment_status == "partially_paid") {
        $rules['amount_paid'] = ['rules' => 'required', 'label' => 'Amount Paid'];
    }

    return $rules;
}

function csrfResponseData($extra = [])
{
    return array_merge($extra, [
        'csrf_token' => csrf_token(),
        'csrf_hash' => csrf_hash(),
    ]);
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
        'status_id' => $ordersItems['status_id'],
        'actions' => $edit_customer,

    ];

    return $result;
}


if (!function_exists('getAppVersion')) {
    function getAppVersion()
    {
        $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC');
        return $version[0]['version'] ?? '1.0';
    }
}

if (!function_exists('getLanguages')) {
    function getLanguages()
    {
        return fetch_details('languages', [], [], null, '0', 'id', 'ASC') ?? [];
    }
}

if (!function_exists('getCustomers')) {
    function getCustomers($business_id)
    {
        return fetch_details("customers", ['business_id' => $business_id]) ?? [];
    }
}

function getData($tableName, $tableData, $page, $optionalData1 = '', $optionalData1Value = '', $optionalData2 = '', $optionalData2Value = '',)
{
    $ionAuth = \Config\Services::ionAuth();
    $settings = get_settings('general', true);
    $languages = getLanguages();
    return [
        'version' => getAppVersion(),
        'code' => session('lang') ?? 'en',
        'current_lang' => session('lang') ?? 'en',
        'languages_locale' => $languages,
        'business_id' => session('business_id'),
        'page' => $page,
        'title' => "Orders - " . $settings['title'] ?? "",
        'from_title' => 'Purchase',
        'meta_keywords' => "subscriptions app, digital subscription, daily subscription, software, app, module",
        'meta_description' => "Home - Welcome to Subscribers, a digital solution for your subscription-based daily problems",
        $tableName => $tableData,
        'user' => $ionAuth->user(session('user_id'))->row(),
        'user_id' => getUserId(),
        'vendor_id' => getUserId(),
        'currency' => $settings['currency_symbol'] ?? '₹',
        $optionalData1 => $optionalData1Value,
        $optionalData2 => $optionalData2Value,
    ];
}
