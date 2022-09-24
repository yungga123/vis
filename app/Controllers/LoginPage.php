<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class LoginPage extends BaseController
{
    public function index()
    {
        $data['title'] = "Welcome to M.I.S.";
        echo view('LoginPage', $data);
    }

    public function login_successful()
    {
        echo "login success";
    }
}
