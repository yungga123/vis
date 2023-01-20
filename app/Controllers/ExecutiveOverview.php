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
            $data['page_title'] = 'Executive Overview Dashboard';
            $data['uri'] = service('uri');

            return view('executive_overview/eo_dashboard',$data);
        }
        else
        {
            return redirect()->to('login');
        }
    }
}
