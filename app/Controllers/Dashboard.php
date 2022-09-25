<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data['title'] = 'Dashboard';

        echo view('templates/header',$data);
        echo view('templates/navbar');
        echo view('templates/sidebar');
        echo view('dashboard/dashboard');
        echo view('templates/footer');

    }
}
