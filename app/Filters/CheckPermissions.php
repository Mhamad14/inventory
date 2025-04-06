<?php

namespace App\Filters;

use App\Libraries\IonAuth;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;

// helper('function_helper'); 

class CheckPermissions implements FilterInterface
{
    protected $ionAuth;
    public function __construct()
    {
        helper('function_helper'); // Load the helper file
        $this->ionAuth = new \App\Libraries\IonAuth();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();


        // Check if $arguments is not null
        if ($arguments !== null) {

            $module = null;
            $action = null;
            foreach ($arguments as $argument) {

                list($key, $value) = explode('=', $argument);

                // Check if the argument specifies a 'module'
                if ($key === 'module') {
                    $module = $value;
                }
                // Check if the argument specifies an 'action'
                elseif ($key === 'action') {
                    $action = $value;
                }
            }


            if (! $this->ionAuth->isAdmin() && ! $this->ionAuth->isDeliveryBoy()) {
                // Check if both 'module' and 'action' are set
                if ($module !== null && $action !== null) {
                    // Check if the user has permission for the specified 'module' and 'action'
                    // print_r((! $this->userHasPermission($module, $action)) ? "does not have permission" :  "has permission" );
                    // die();

                    if (! $this->userHasPermission($module, $action)) {
                        // If the user doesn't have permission, check their role

                        // Redirect or display an error message, depending on your needs
                        $session->setFlashdata("message", "You do not have permission to access this page.");
                        $session->setFlashdata("type", "error");

                        return redirect()->to(site_url('admin/home'));
                    }
                }
            }
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Code after the request
    }

    private function userHasPermission($moduleName, $action)
    {
        $session = Services::session();

        $action_from_route = $action;

        // Load the Upbiz configuration
        $upbizConfig = new \Config\Upbiz();
        $all_permissions = $upbizConfig->permissions;

        $userPermissions = [];

        $userPermissions = $this->get_user_permissions($session->get('user_id'));
        $user_exists = empty($userPermissions) ? false : true;

        if ($user_exists) {
            $userPermissions = json_decode($userPermissions[0]['permissions'], true);
        } else {
            return false;
        }

        if (isset($userPermissions["'$moduleName'"])) {

            if (in_array($action_from_route, $all_permissions[$moduleName]) && in_array("'$action_from_route'", $userPermissions["'$moduleName'"])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }



    // Function to get the module name from the URL segment private function getModuleName()
    private function getModuleName()
    {
        // Get the current request object
        $request = Services::request();

        // Get the segments from the URI
        $segments = $request->uri->getSegments();

        // Initialize moduleName as an empty string
        $moduleName = '';

        // Assuming that the module name is the second segment after 'vendor'
        if (isset($segments[1])) {
            $moduleName = $segments[1];
        }



        return $moduleName;
    }

    function get_user_permissions($id)
    {
        $userData = fetch_details('team_members', ['user_id' => $id]);
        // echo "User Permissions Data: " . json_encode($userData) . "<br>";

        return $userData;
    }
}
