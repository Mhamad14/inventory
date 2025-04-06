<?php

namespace App\Filters;

use App\Controllers\admin\Team_members;
use App\Models\Team_members_model;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;


class CheckRoles implements FilterInterface
{

    public function __construct()
    {
        helper('function_helper'); // Load the helper file
    }
    public function before(RequestInterface $request, $arguments = null)
    {
        
        $session = session();
        // Get the currently authenticated user's ID from the session or wherever it's stored
        $user_id = session()->get('user_id'); // Adjust this based on your authentication implementation

        // Fetch the user's roles from the database
        $team_member_model = new Team_members_model();
        $team_member = $team_member_model->select()->where('user_id', $user_id )->get()->getResultArray();
    
        // check if user is team member or not 
        $is_team_member =  ! empty($team_member)  ; // if the $team_member is empty then user is not a team_member

            if($is_team_member){
                
                $session->setFlashdata("message", "You do not have permission to access this page.");
                $session->setFlashdata("type", "error");
                return redirect()->to(site_url('admin/home'));
            }

        return $request;
    }



    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action required after the request.
    }

    // protected function isUserRoleInRestrictedRoles($restrictedRoles)
    // {
    //     // Your logic to fetch the user's role or determine the user's role
    //     // Replace this with your actual logic to get the user's role
    //     $userRole = 3; // Replace with the user's role (e.g., fetched from the database or session)

    //     return in_array($userRole, $restrictedRoles);
    // }
}
