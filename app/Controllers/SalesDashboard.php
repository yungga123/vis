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
            $data['page_title'] = 'Sales Dashboard';
            $data['count_tasklead'] = count($taskleadModel->findAll());

            return view('sales_dashboard/sales_dashboard',$data);
        }
        else
        {
            return redirect()->to('login');
        }
    }
}
