<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TaskLeadModel;

class SalesManager extends BaseController
{
    public function index()
    {
        if (session('logged_in')==true)
        {
            $data['title'] = 'Manager of Sales';
            $data['page_title'] = 'Manager of Sales';
            $data['uri'] = service('uri');

            return view('manager_of_sales/menu',$data);
        }
        else
        {
            return redirect()->to('login');
        }
    }

    public function consolidated_forecast() {
        if (session('logged_in')==false) {
            return redirect()->to('login');
        }

        $taskleadModel = new TaskLeadModel();

        $data['title'] = 'Consolidated Sales Forecast';
        $data['page_title'] = 'Consolidated Sales Forecast';
        $data['uri'] = service('uri');
        $data['bookedNumber'] = $taskleadModel->where('employee_id',session('employee_id'))->where('status','100.00')->find();
        $data['negotiationNumber'] = $taskleadModel->where('employee_id',session('employee_id'))->where('status','90.00')->find();
        $data['evalNumber'] = $taskleadModel->where('employee_id',session('employee_id'))->where('status','70.00')->find();
        $data['devsolNumber'] = $taskleadModel->where('employee_id',session('employee_id'))->where('status','50.00')->find();
        $data['qualifiedNumber'] = $taskleadModel->where('employee_id',session('employee_id'))->where('status','30.00')->find();
        $data['identifiedNumber'] = $taskleadModel->where('employee_id',session('employee_id'))->where('status','10.00')->find();


        return view('manager_of_sales/sales_forecast', $data);
    }
}
