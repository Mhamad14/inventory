<?php


function getUserId()
{
    $ionAuth = \Config\Services::ionAuth();
    $user_id = $_SESSION['user_id'];
    return $ionAuth->isTeamMember() ? get_vendor_for_teamMember($user_id) : $user_id;
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
    $result =  [
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
        
    ];
    if (!empty($optionalData1)) {
        $result[$optionalData1] = $optionalData1Value;
    }
    if (!empty($optionalData2)) {
        $result[$optionalData2] = $optionalData2Value;
    }
        return $result;

}
