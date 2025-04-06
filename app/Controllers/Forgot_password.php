<?php

namespace App\Controllers;


use App\Controllers\Frontend;

class Forgot_password extends Frontend
{
    protected $ionAuth;
    protected $validation;

    public function __construct()
    {

        parent::__construct();
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
    }
    public function index()
    {

        $settings = get_settings('general', true);
        $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
        $data['half_logo'] = (isset($settings['half_logo'])) ? $settings['half_logo'] : "";
        $data['favicon'] = (isset($settings['favicon'])) ? $settings['favicon'] : "";
        $company_title = (isset($settings['title'])) ? $settings['title'] : "UpBiz";
        $data['company'] = $company_title;
        $data['title'] = "Reset Password &mdash; $this->appName ";
        $data['page'] = VIEWS . 'forgot_password';
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "$this->appName an digital solution for your subscription based daily problems";
        return view("frontend/template", $data);
    }



    public function showResetForm()
    {
        // Step 1: Retrieve reset token from the GET request
        $token = $this->request->getGet('token');

        // Step 2: Fetch user details based on the reset token
        $userDetails = fetch_details('users', ['forgotten_password_code' => $token]);
        $userDetails = isset($userDetails[0]) ? $userDetails[0] : null;

        // Step 3: Check if the token is valid and not expired
        if (!$userDetails || strtotime($userDetails['forgotten_password_time']) < time()) {
            // If token is invalid or expired, set error message and redirect to forgot password page
            session()->setFlashdata('message', ['Invalid or expired token.']);
            session()->setFlashdata('type', 'error');
            return redirect()->to('forgot_password');
        }

        $settings = get_settings('general', true);
        $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
        $data['half_logo'] = (isset($settings['half_logo'])) ? $settings['half_logo'] : "";
        $data['favicon'] = (isset($settings['favicon'])) ? $settings['favicon'] : "";
        $company_title = (isset($settings['title'])) ? $settings['title'] : "UpBiz";
        $data['company'] = $company_title;
        $data['title'] = "Reset Password &mdash; $this->appName ";
        $data['page'] = VIEWS . 'reset_password';
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "$this->appName an digital solution for your subscription based daily problems";

        // Step 6: Include the reset token in data to render in the view
        $data['token'] = $token;

        return view("frontend/template", $data);
    }

    /**
     * Updates the user's password after verifying the reset token, email, and mobile identity.
     * 
     * This method validates user inputs, checks the validity and expiration of the reset token,
     * and securely updates the password if all checks pass. If validation fails, it returns errors
     * to the user. Additionally, it clears the reset token after successful update to prevent reuse.
     * 
     * @return \CodeIgniter\HTTP\Response JSON response with success or error status.
     */
    public function update_password()
    {
        // Step 1: Set validation rules for input fields
        $this->validation->setRules([
            'email' => [
                'rules' => 'required',
                'label' => 'Email'
            ],
            'identity' => [
                'rules' => 'required',
                'label' => 'Mobile'
            ],
            'new_password' => [
                'rules' => 'required',
                'label' => 'Password'
            ],
            'confirm_password' => [
                'rules' => 'required|matches[new_password]',
                'label' => 'Confirm password'
            ],
        ]);

        // Step 2: Validate input
        if (!$this->validation->withRequest($this->request)->run()) {
            // If validation fails, return errors in JSON format
            $errors = $this->validation->getErrors();
            $response = [
                'error' => true,
                'message' => $errors,
                'data' => []
            ];
            // Add CSRF tokens to the response for security
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }

        // Step 3: Retrieve input values from the request
        $token = $this->request->getPost('token');  // Reset token
        $newPassword = $this->request->getPost('new_password'); // New password
        $emailAddress = $this->request->getPost('email'); // Email address
        $identity = $this->request->getPost('identity'); // Mobile identity

        // Step 4: Check if the reset token is valid and has not expired
        $userDetails = fetch_details('users', ['forgotten_password_code' => $token, 'email' => $emailAddress, 'mobile' => $identity,]);
        $userDetails = isset($userDetails[0]) ? $userDetails[0] : null;

        if (!$userDetails || strtotime($userDetails['forgotten_password_time']) < time()) {
            // If token is invalid or expired, return an error and redirect to forgot password page
            $response = [
                'error' => true,
                'message' => ['Invalid user details, please check email and mobile'],
                'data' => [
                    'redirect_link' => base_url('forgot_password')
                ]
            ];
            // Add CSRF tokens to maintain security
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }

        // Step 5: Prepare data to update the password and clear the reset token
        $updateData = [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT), // Hash the new password
            'forgotten_password_code' => null, // Clear the token to prevent reuse
            'forgotten_password_time' => null, // Clear token expiration time
        ];

        // Step 6: Update the password in the database
        $db = \Config\Database::connect();
        $updateResult = $db->table('users')->where([
            'email' => $emailAddress,
            'mobile' => $identity,
        ])->update($updateData);

        // Step 7: Check if the update was successful and send appropriate response
        if ($updateResult) {
            // If update is successful, return a success message with a redirect link to login
            $response = [
                'error' => false,
                'message' => ['Password updated successfully.'],
                'data' => []
            ];
        } else {
            // If update failed, return an error message
            $response = [
                'error' => true,
                'message' => ['Failed to update password. Please try again.'],
                'data' => []
            ];
        }

        // Step 8: Return CSRF tokens in the response for security and finalize the response
        $response['csrf_token'] = csrf_token();
        $response['csrf_hash'] = csrf_hash();
        return $this->response->setJSON($response);
    }


    /**
     * Verify user credentials for password reset.
     *
     * This function validates the user's email and mobile identity fields to check if
     * the user exists in the database. If validation passes and the user exists, it sends
     * a reset password email to the user's registered email address. If any step fails,
     * appropriate error messages are set in session flash data, and the user is redirected
     * back to the forgot password page.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirects the user back to the forgot password page with success or error messages.
     */
    public function verify()
    {
        // Check if modification is allowed; if not, show error message in demo mode
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            session()->setFlashdata('message', [DEMO_MODE_ERROR]);
            session()->setFlashdata('type', 'error');
            return redirect()->to('forgot_password');
        }

        // Set validation rules for email and mobile identity fields
        $this->validation->setRules([
            'email' => [
                'rules' => 'required',
                'label' => 'Email'
            ],
            'identity' => [
                'rules' => 'required',
                'label' => 'Mobile'
            ],
        ]);

        // If validation fails, set error messages in session and redirect
        if (!$this->validation->withRequest($this->request)->run()) {
            session()->setFlashdata('message', $this->validation->getErrors());
            session()->setFlashdata('type', 'error');
            return redirect()->to('forgot_password');
        }

        // Retrieve email and identity values from the request
        $emailAddress = $this->request->getVar('email');
        $identity = $this->request->getVar('identity');

        // Check if user with the provided email and mobile exists
        $user = fetch_details('users', ['email' => $emailAddress, 'mobile' => $identity]);

        if (isset($user[0])) {
            $user = $user[0];
            $email = \Config\Services::email();
            $setting = get_settings(type: 'email', is_json: true);
            $company_title = get_settings('general', true);

            // Prepare email content
            $token = bin2hex(random_bytes(50)); // Use a random, secure token
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour')); // Set expiration time

            $reset_link = base_url("forgot_password/reset_password/?token=$token");
            $subject = "Forgot Password";
            $message = $this->email_template(reset_link: $reset_link, company_title: $company_title['title']);

            // Configure email settings
            $email_con = [
                'protocol'  => 'smtp',
                'SMTPHost'  => $setting['smtp_host'],
                'SMTPPort'  => (int) $setting['smtp_port'],
                'SMTPUser'  => $setting['email'],
                'SMTPPass'  => $setting['password'],
                'SMTPCrypto' => $setting['smtp_encryption'],
                'mailType'  => $setting['mail_content_type'],
                'charset'   => 'utf-8',
                'wordWrap'  => true,
            ];
            $email->initialize($email_con);
            $email->setFrom($setting['email'], $company_title['title']);
            $email->setTo(trim($emailAddress));
            $email->setSubject($subject);
            $email->setMessage($message);

            // Attempt to send the email
            if ($email->send()) {


                $db = \Config\Database::connect();
                $db->table('users')->where([
                    'email' => $emailAddress,
                    'mobile' => $identity,
                ])->update(['forgotten_password_code' => $token, 'forgotten_password_time' => $expires]);


                // Email sent successfully; set success message in session
                session()->setFlashdata('message', ['A reset password link has been sent to your registered email address. Please check your inbox.']);
                session()->setFlashdata('type', 'success');
                return redirect()->to('forgot_password');
            } else {
                // Email sending failed; set error message in session
                session()->setFlashdata('message', ['Unable to send email. Please contact the admin.']);
                session()->setFlashdata('type', 'error');
                return redirect()->to('forgot_password');
            }
        } else {
            // User not found; set error message in session
            session()->setFlashdata('message', ['Invalid details!']);
            session()->setFlashdata('type', 'error');
            return redirect()->to('forgot_password');
        }
    }


    private function email_template($reset_link, $company_title)
    {
        return  '
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Reset Your Password</title>
                        <style>
                            /* Reset and email styling */
                          
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f4f4f4;
                                margin: 0;
                                padding: 0;
                            }
                            .container {
                                width: 100%;
                                padding: 20px;
                                background-color: #f4f4f4;
                            }
                            .email-content {
                                max-width: 600px;
                                margin: 0 auto;
                                background-color: #ffffff;
                                padding: 20px;
                                border-radius: 5px;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                text-align: center;
                            }
                            .email-header {
                                font-size: 24px;
                                font-weight: bold;
                                color: #333333;
                            }
                            .email-text {
                                font-size: 16px;
                                color: #555555;
                                margin: 20px 0;
                            }
                            .btn {
                                display: inline-block;
                                padding: 12px 24px;
                                background-color: #007bff;
                                color: #ffffff;
                                text-decoration: none;
                                border-radius: 5px;
                                font-size: 16px;
                                font-weight: bold;
                                margin-top: 20px;
                            }
                            .btn:hover {
                                background-color: #0056b3;
                            }
                            .footer {
                                margin-top: 30px;
                                font-size: 14px;
                                color: #777777;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="email-content">
                                <h1 class="email-header">Reset Your Password</h1>
                                <p class="email-text">
                                    You recently requested to reset your password. Click the button below to set a new password:
                                </p>
                                <a href="' . $reset_link . '" class="btn">Reset Password</a>
                                <p class="email-text">
                                    If you did not request a password reset, please ignore this email or contact support if you have questions.
                                </p>
                                <div class="footer">
                                    <p>Thank you,<br>' . $company_title . '</p>
                                </div>
                            </div>
                        </div>
                    </body>
                    </html>
                    ';
    }
}
