<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Inventory extends BaseController
{
    public function index()
    {
        if (session('logged_in')==false) {
            return redirect()->to('login');
        }

        $data['title'] = 'Inventory Menu';
        $data['page_title'] = 'Inventory Menu';

        return view('inventory/menu',$data);
    }

    public function add_item() {
        if (session('logged_in')==false) {
            return redirect()->to('login');
        }

        $data['title'] = 'Add Item';
        $data['page_title'] = 'Add Item';

        return view('inventory/add_item',$data);
    }

}
