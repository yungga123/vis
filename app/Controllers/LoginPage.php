<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Accounts;
use App\Models\EmployeesModel;

class LoginPage extends BaseController
{

    protected $helpers = ['form'];

    public function index()
    {
        $data['title'] = "Welcome to M.I.S.";
        echo view('LoginPage', $data);
    }

    public function login_successful()
    {
        echo "login success";
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

        if ($user_find)
        {
            $username = $user_find[0]['username'];
            $password = $user_find[0]['password'];
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
