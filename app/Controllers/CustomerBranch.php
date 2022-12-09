<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerBranchModel;
use App\Models\CustomersModel;

class CustomerBranch extends BaseController
{
    public function index()
    {
        if (session('logged_in') == true) {
            
            $customersModel = new CustomersModel();
            $data['title'] = 'Add Customer Branch';
            $data['page_title'] = 'Add Customer Branch';
            $data['customers'] = $customersModel->findAll();

            echo view('templates/header', $data);
            echo view('customers_branch/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('customers_branch/add_customer_branch');
            echo view('templates/footer');
            echo view('customers_branch/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function add_customer_validate() {
        $customersBranchModel = new CustomerBranchModel();

        $validate = [
            "success" => false,
            "messages" => ''
        ];

        $data = [
            "customer_id" => $this->request->getPost('customer_id'),
            "branch_name" => $this->request->getPost('branch_name'),
            "address_province" => $this->request->getPost('address_province'),
            "address_city" => $this->request->getPost('address_city'),
            "address_brgy" => $this->request->getPost('address_brgy'),
            "address_sub" => $this->request->getPost('address_sub'),
            "contact_number" => $this->request->getPost('contact_number'),
            "contact_person" => $this->request->getPost('contact_person'),
            "email_address" => $this->request->getPost('email_address'),
            "notes" => $this->request->getPost('notes')
        ];

        if (!$customersBranchModel->insert($data)) {
            $validate['messages'] = $customersBranchModel->errors();
        } else {
            $validate['success'] = true;
        }

        echo json_encode($validate);
    }
}
