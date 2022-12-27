<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TaskLeadModel;

class SalesDashboard extends BaseController
{
    public function index()
    {
        if (session('logged_in')==true)
        {
            $taskleadModel = new TaskLeadModel();
            $data['title'] = 'Sales Dashboard';
            $data['count_tasklead'] = count($taskleadModel->findAll());
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
