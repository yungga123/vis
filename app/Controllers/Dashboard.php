<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {

        if (session('logged_in')==true)
        {
            $data['title'] = 'Dashboard';
            $data['page_title'] = 'Dashboard';
            $data['uri'] = service('uri');

            return view('dashboard/dashboard',$data);
        }
        else
        {
            return redirect()->to('login');
        }
    }
}
