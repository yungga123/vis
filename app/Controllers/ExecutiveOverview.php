<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ExecutiveOverview extends BaseController
{
    public function index()
    {
        if (session('logged_in')==true)
        {
            $data['title'] = 'Executive Overview Dashboard';

            echo view('templates/header',$data);
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('executive_overview/eo_dashboard');
            echo view('templates/footer');
            echo view('executive_overview/script');
        }
        else
        {
            return redirect()->to('login');
        }
    }
}
