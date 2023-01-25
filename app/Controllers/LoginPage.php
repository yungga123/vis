<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Accounts as AccountsModel;
use App\Models\EmployeesModel;

class LoginPage extends BaseController
{

    protected $helpers = ['form'];

    public function index()
    {
        $data['title'] = "Welcome to M.I.S.";
        echo view('login', $data);
    }

    public function login_successful()
    {
        echo "login success";
    }

    public function login()
    {
        $data = [];
        
        try {
            $rules = [
                'username' => 'required|alpha_numeric|min_length[4]|max_length[20]',
                'password' => 'required|min_length[8]|max_length[20]'
            ];
            
            if ($this->validate($rules)) {
                $accountsModel  = new AccountsModel();
                $username       = $this->request->getVar('username');
                $password       = $this->request->getVar('password');

                $data['status']         = self::STATUS_ERROR;
                $data['message']        = 'Wrong username or password. Please try again!';

                if ($user = $accountsModel->authenticate($username, $password)) {
                    $data['status']     = self::STATUS_SUCCESS;
                    $data['message']    = 'You have successfully logged in! Please wait while redirecting...';

                    $employeesModel = new EmployeesModel();
                    $employee = $employeesModel->where('employee_id', $user['employee_id'])->first();

                    $session = session();
                    $session->set([
                        'logged_in'     => true,
                        'username'      => $username,
                        'access_level'  => $user['access_level'],
                        'access'        => $user['access_level'],
                        'employee_id'   => $employee['employee_id'],
                        'name'          => $employee['firstname'].' '.$employee['lastname'],
                        'logged_at'     => date('Y-m-d H:i:s'),
                    ]);
                }
            } else {
                $data['status']     = self::STATUS_ERROR;
                $data['message']    = 'Validation error!';
                $data['errors']     = $this->validator->getErrors();
            }
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status'] = self::STATUS_ERROR;
            // $data['errors']     = $e->getMessage();
            $data['message'] = $e->getMessage();
        }

        return $this->response->setJSON($data); 
    }

    public function sign_in()
    {
        $validate_msg = [
			'success' => false,
			'errors' => ''

		];

        $validation = \Config\Services::validation();
        $session = session();
        $userModel = new Accounts();
        $employeesModel = new EmployeesModel();
        $username = $this->request->getPost('username');
        $user_find = $userModel->findUsername($username);
        $password = '';
        $access = '';

        if ($user_find)
        {
            $username = $user_find[0]['username'];
            $password = $user_find[0]['password'];
            $access = $user_find[0]['access_level'];
            $employee_id = $user_find[0]['employee_id'];
        }

        $validate = $this->validate(
            [
                'username' => "required|max_length[50]|is_not_unique[accounts.username]",
                'password' => "required|max_length[50]|in_list[$password]"
            ],
            [
                'username' => [
                    'required' => "Please enter a username.",
                    'max_length' => "Username is limited to 50 characters.",
                    "is_not_unique" => "Username does not exist"
                ],
                'password' => [
                    'required' => "Please enter a password.",
                    'max_length' => "Password is limited to 50 characters.",
                    "in_list" => "Wrong Password."
                ]
            ]
        );

        

        if($validate)
        {
            $employeeFind = $employeesModel->where('employee_id',$employee_id)->findAll();
            $user_data = [
                'logged_in' => true,
                'username' => $username,
                'password' => $password,
                'name' => $employeeFind[0]['firstname'].' '.$employeeFind[0]['lastname'],
                'access' => $access,
                'employee_id' => $employeeFind[0]['employee_id']
            ];

            $session->set($user_data);

            $validate_msg['success'] = true;
        }
        else
        {
            $validate_msg['errors'] = $validation->getErrors();
            
        }
        echo json_encode($validate_msg);
    }

    public function logout()
    {
        $session = session();

        $session->destroy();

        return redirect()->to('');
    }
}
