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

            switch (session('access')) {
                case 'admin':
    
                    break;
    
                case 'ofcadmin':
    
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
            
            return view('admin_dashboard/admin_dashboard',$data);
        } else {
            return redirect()->to('login');
        }
    }
}
