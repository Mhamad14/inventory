<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ionAuth = \Config\Services::ionAuth();
        $session = session();
        // $response = service('response');

        // $currentUrl = current_url();
        // // Skip checks for JSON/AJAX requests
        // if ($request->isAJAX() && strpos(current_url(), 'admin/products/json') !== false) {
        //     if (!$ionAuth->loggedIn()) {
        //         return $response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        //     }
        //     return null;
        // }
        if (!$ionAuth->loggedIn() || (!$ionAuth->isAdmin() && !$ionAuth->isTeamMember())) {
            return redirect()->to('login')->with('error', 'Unauthorized access.');
        }

        if (!$session->has('business_id')) {
            $businessModel = new \App\Models\Businesses_model();
            $allBusinesses = $businessModel->findAll();

            $session->setFlashdata([
                'message' => empty($allBusinesses) ? 'Please create a business!' : 'Please select a business!',
                'type'    => 'error',
            ]);

            return redirect()->to('admin/businesses');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Not needed here
    }
}
