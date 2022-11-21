<?php

namespace App\Controllers;
use App\Controllers\BaseController;



class Customers extends BaseController
{
    
    public function index()
    {
        

        if (session('logged_in') == true) {
            
            $data['title'] = 'Add Customer';
            echo view('templates/header', $data);
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('customers/add_customer');
            echo view('templates/footer');
            echo view('customers/script');
        } else {
            return redirect()->to('login');
        }
    }
}
