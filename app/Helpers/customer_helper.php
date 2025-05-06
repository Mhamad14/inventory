<?php


function prepareCustomerRow(array $customer): array
{
    // Generate the status badge
    $status = $customer['status']
        ? '<span class="text-success"><span class="text-success">Active</span>'
        : '<span class="text-muted"><span class="text-muted">Diactivated</span>';

    // Format the customer's name
    $name = ucwords($customer['first_name']);

    // Generate action buttons
    $edit_customer = "<a href='javascript:void(0)' data-id='{$customer['user_id']}' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Status update' data-bs-toggle='modal' data-bs-target='#customer_status'><i class='bi bi-pen'></i></a>";
    $details_btn = "<a href='" . base_url("admin/customers/".$customer['user_id'].'/edit/' ) . "' class='btn btn-info btn-sm'><i class='bi bi-eye'></i></a>";

    // Prepare the row data
    $result = [
        'id' => $customer['user_id'],
        'customer_id' => $customer['id'],
        'name' => $name ,
        'email' => htmlspecialchars($customer['email']),
        'mobile' => htmlspecialchars($customer['mobile']),
        'balance' => currency_location(decimal_points($customer['balance'])),
        'debit' => currency_location(decimal_points($customer['debit'] ?? 0)),
        'status' => $status,
        'active' => $customer['status'],
        'actions' => $details_btn . ' ' . $edit_customer
    ];

    return $result;
}

function prepareCustomerOrdersRow(array $customerOrders): array
{
    // Generate the status badge
    $status = "";
    if ($customerOrders['payment_status'] == "fully_paid") {
        $status = "<span class='badge badge-success'>Fully Paid</span>";
    }
    if ($customerOrders['payment_status'] == "partially_paid") {
        $status = "<span class='badge badge-primary'>Partially Paid</span>";
    }
    if ($customerOrders['payment_status'] == "unpaid") {
        $status = "<span class='badge badge-warning'>Unpaid</span>";
    }
    if ($customerOrders['payment_status'] == "cancelled") {
        $status = "<span class='badge badge-danger'>Cancelled</span>";
    }
    if (empty($customerOrders['payment_status'])) {
        $status = "<span class='badge badge-secondery'>Not decided</span>";
    }

    // Generate action buttons
    // $edit_customer = "<a href='javascript:void(0)' data-id='{$customerOrders['user_id']}' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Status update' data-bs-toggle='modal' data-bs-target='#customer_status'><i class='bi bi-pen'></i></a>";
    // $details_btn = "<a href='" . base_url("admin/customers/".$customerOrders['user_id'].'/edit/' ) . "' class='btn btn-info btn-sm'><i class='bi bi-eye'></i></a>";
    
    // generate typeIcon
    $typeIcone = "";
    $order_type = $customerOrders['order_type'] ?? '';
    if ($order_type == "service") {
        $typeIcone = "<i class='bi bi-gear text-primary'></i><span>"." ".labels('services', 'Service')."</span>";
    }
    else if($order_type == "product"){
        $typeIcone = "<i class='bi bi-border-bottom text-info'></i><span>"." ".labels('products', 'Product')."</span>";
    }
    $result = [
        'id' => $customerOrders['id'],
        'order_date' => $customerOrders['created_at'],
        'order_type' => $typeIcone ,
        'discount' => currency_location(decimal_points($customerOrders['discount'])),
        'final_total' => currency_location(decimal_points($customerOrders['final_total'])),
        'amount_paid' => currency_location(decimal_points($customerOrders['amount_paid'])),
        'returns_amount' => currency_location(decimal_points($customerOrders['returns_total'])),
        'debt' => currency_location(decimal_points($customerOrders['debt'])),
        'payment_status' => $status,
        // 'status' => $status,
        // 'active' => $customerOrders['status'],
        // 'actions' => $details_btn . ' ' . $edit_customer
    ];
   

    return $result;
}


if (!function_exists('setJSON')) {
    function setJSON($response, bool $error, $message)
    {
        return $response->setJSON([
            'error' => $error,
            'message' => $message,
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
            'data' => []
        ]);
    }
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