<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TaskLeadModel;

class SalesDashboard extends BaseController
{
    public function index()
    {

        if (session('logged_in') == false) {
            return redirect()->to('login');
        }

        switch (session('access')) {
            case 'admin':

                break;

            case 'sales':

                break;

            case 'manager':

                break;
            
            default:
                $data['title'] = 'Invalid Access!!';
                $data['page_title'] = 'Invalid Access!!';
                $data['href'] = site_url('dashboard');

                return view('templates/offlimits',$data);
                break;
        }

        $taskleadModel = new TaskLeadModel();
        $data['title'] = 'Sales Dashboard';
        $data['page_title'] = 'Sales Dashboard';
        $data['count_tasklead'] = count($taskleadModel->findAll());

        return view('sales_dashboard/sales_dashboard', $data);
    }
}
