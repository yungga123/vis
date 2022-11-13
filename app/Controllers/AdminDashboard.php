<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdminDashboard extends BaseController
{
    public function index()
    {
        if (session('logged_in')==true)
        {
            $data['title'] = 'Dashboard';

            echo view('templates/header',$data);
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('admin_dashboard/admin_dashboard');
            echo view('templates/footer');
            echo view('admin_dashboard/script');
        }
        else
        {
            return redirect()->to('login');
        }
    }
}
