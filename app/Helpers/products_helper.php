<?php

function getUserId()
{
    $ionAuth = \Config\Services::ionAuth();
    $user_id = $_SESSION['user_id'];
    return $ionAuth->isTeamMember() ? get_vendor_for_teamMember($user_id) : $user_id;
}

function prepareProductsRow(array $products): array
{
    $business_name = isset($_SESSION['business_name']) ? $_SESSION['business_name'] : "";

    $status = ($products['status'] == 1)
        ? "<span class='badge badge-primary'>Active</span>"
        : "<span class='badge' style='background-color:#ed1307'>Deactive</span>";

    $products['image'] = '<div class="image-box-100 "><a href="' . base_url($products['image'])  . '" data-lightbox="image-1">
             <img src="' . base_url($products['image']) . '" class="image-100 image-box-100 img-fluid" />
            </a></div>';

    $edit_product = "<a href='" . site_url('admin/products/edit_product') . "/" . $products['product_id'] . "' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Edit'><i class='bi bi-pencil'></i></a> ";
    $edit_product .= "<a href='javascript:void(0)' data-id='" . $products['product_id'] . "' class='btn btn-warning btn-sm' data-toggle='tooltip' data-placement='bottom' title='View' data-bs-toggle='modal' data-bs-target='#variants_Modal'><i class='bi bi-eye'></i></a>";
    $edit_product .= "<a href='javascript:void(0)' onclick='generate_barcode(" . $products['product_id'] . ")' class='btn btn-info btn-sm m-1' data-toggle='tooltip' data-placement='bottom' title='Barcode' data-bs-toggle='modal' data-bs-target='#barcode_Modal'><i class='bi bi-upc-scan'></i></a>";

    $result = [
        'id' => $products['product_id'],
        'name' => ucwords($products['product_name']),
        'description' => $products['description'],
        'image' => $products['image'],
        'stock' => $products['stock'],
        'status' => $status,
        'categorey_id' => 'category_id',
        'caregory_name' => 'caregory_name',
        'brand_id' => 'brand_id' ?? "",
        'brand_name' => 'brand_name',
        'creator' => $products['creator'],
        'business_name' => $business_name,
        'action' => $edit_product
    ];

    return $result;
}

function csrfResponseData($extra = [])
{
    return array_merge($extra, [
        'csrf_token' => csrf_token(),
        'csrf_hash' => csrf_hash(),
    ]);
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


