<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdminDashboard extends BaseController
{
    public function index()
    {
        if (session('logged_in') == true) {
            $data['title'] = 'Admin Dashboard';
            $data['page_title'] = 'Admin Dashboard';
            $data['uri'] = service('uri');
            
            return view('admin_dashboard/admin_dashboard',$data);
        } else {
            return redirect()->to('login');
        }
    }
}
