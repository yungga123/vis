<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SalesDashboard extends BaseController
{
    public function index()
    {
        if (session('logged_in')==true)
        {
            $data['title'] = 'Sales Dashboard';

            echo view('templates/header',$data);
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('sales_dashboard/sales_dashboard');
            echo view('templates/footer');
            echo view('sales_dashboard/script');
        }
        else
        {
            return redirect()->to('login');
        }
    }
}
