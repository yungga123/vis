<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data['title']          = 'Dashboard';
        $data['page_title']     = 'Dashboard';
        $data['uri']            = service('uri');
        $data['modules']        = $this->modules;

        return view('dashboard/dashboard', $data);
    }
}
