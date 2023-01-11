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
            $data['uri'] = service('uri');

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

    public function edit_customer_branch($id) {

        $customersModel = new CustomersModel();
        $customerBranchModel = new CustomerBranchModel();

        if ($this->request->getMethod() == 'post') {
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
    
            if (!$customerBranchModel->update($id,$data)) {
                $validate['messages'] = $customerBranchModel->errors();
            } else {
                $validate['success'] = true;
            }
    
            return json_encode($validate);
        }

        if (session('logged_in')==false) {
            return redirect()->to('login');
        }

        $data['title'] = 'Edit Customer Branch';
        $data['page_title'] = 'Edit Customer Branch';
        $data['customers'] = $customersModel->findAll();
        $data['customerBranchModel'] = $customerBranchModel->find($id);
        $data['uri'] = service('uri');

        return view('templates/header', $data)
            . view('customers_branch/header')
            . view('templates/navbar')
            . view('templates/sidebar')
            . view('customers_branch/add_customer_branch')
            . view('templates/footer')
            . view('customers_branch/edit_script')
            . view('customers_branch/script');
    }

    public function delete_customer_branch($id) {

        if (session('logged_in') == false) {

            return redirect()->to('login');

        }

        $customerBranchModel = new CustomerBranchModel();

        $data['title'] = 'Delete Customer Branch';
        $data['page_title'] = 'Delete Customer Branch';
        $data['uri'] = service('uri');
        $data['href'] = site_url('customers-list');
        $customerBranchModel->delete($id);

        return view('templates/header', $data)
            . view('customers/header')
            . view('templates/navbar')
            . view('templates/sidebar')
            . view('templates/deletepage')
            . view('templates/footer')
            . view('customers/script');
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
