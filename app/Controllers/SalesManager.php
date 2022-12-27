<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SalesManager extends BaseController
{
    public function index()
    {
        if (session('logged_in')==true)
        {
            $data['title'] = 'Manager of Sales';
            $data['page_title'] = 'Manager of Sales';
            $data['uri'] = service('uri');

            echo view('templates/header',$data);
            echo view('manager_of_sales/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('manager_of_sales/menu');
            echo view('templates/footer');
            echo view('manager_of_sales/script');
        }
        else
        {
            return redirect()->to('login');
        }
    }
}
