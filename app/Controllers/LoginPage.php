<?php

namespace App\Controllers;

use App\Controllers\BaseController;

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

        $validate = $this->validate(
            [
                'username' => 'required|max_length[50]',
                'password' => 'required|max_length[50]'
            ],
            [
                'username' => [
                    'required' => 'Please enter a username.',
                    'max_length' => 'Username is limited to 50 characters.'
                ],
                'password' => [
                    'required' => 'Please enter a password.',
                    'max_length' => 'Password is limited to 50 characters.'
                ]
            ]
        );

        if($validate)
        {
            echo "Success";
        }
        else
        {
            echo $validation->listErrors();
        }
    }
}
