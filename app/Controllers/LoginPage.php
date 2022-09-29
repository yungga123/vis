<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Accounts;

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
        $validation = \Config\Services::validation();
        $session = session();
        $userModel = new Accounts();
        $username = $this->request->getPost('username');
        $user_find = $userModel->findUsername($username);
        $password = '';

        if ($user_find)
        {
            $username = $user_find[0]['username'];
            $password = $user_find[0]['password'];
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
            $user_data = [
                'logged_in' => true,
                'username' => $username,
                'password' => $password
            ];

            $session->set($user_data);

            return redirect()->to('');
        }
        else
        {
            echo $validation->listErrors();
        }
    }
}
