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

if (!function_exists('handleMissingBusiness')) {
    function handleMissingBusiness()
    {
        $business_model = new \App\Models\Businesses_model();
        $all_businesses = $business_model->findAll();

        if (empty($all_businesses)) {
            session()->setFlashdata('message', 'Please create a business!');
            session()->setFlashdata('type', 'error');
        } else {
            session()->setFlashdata('message', 'Please select a business!');
            session()->setFlashdata('type', 'error');
        }

        return redirect()->to('admin/businesses');
    }
}