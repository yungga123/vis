<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerBranchModel;
use App\Models\CustomersVtBranchModel;
use App\Models\CustomersVtBranchViewModel;
use App\Models\CustomersVtModel;
use App\Models\CustomersVtViewModel;
use monken\TablesIgniter;

class CustomersVt extends BaseController
{
    public function index()
    {
        if (session('logged_in')==false) {
            return redirect()->to('login');
        }

        $data['title'] = 'Customers';
        $data['page_title'] = 'Customers Menu';
        $data['uri'] = service('uri');

        return view('customers_vt/menu',$data);

    }

    public function add_customervt() {

        if ($this->request->getMethod() == 'post') {
            $customersVtModel = new CustomersVtModel();

            $validate = [
                "success" => false,
                "messages" => ''
            ];

            $data = [
                "customer_type" => $this->request->getPost('customer_type'),
                "customer_name" => $this->request->getPost('customer_name'),
                "contact_person" => $this->request->getPost('contact_person'),
                "notes" => $this->request->getPost('notes'),
                "contact_number" => $this->request->getPost('contact_number'),
                "email_address" => $this->request->getPost('email_address'),
                "source" => $this->request->getPost('source'),
                "address_province" => $this->request->getPost('address_province'),
                "address_city" => $this->request->getPost('address_city'),
                "address_brgy" => $this->request->getPost('address_brgy'),
                "address_sub" => $this->request->getPost('address_sub')
            ];

            if (!$customersVtModel->insert($data)) {
                $validate['messages'] = $customersVtModel->errors();
            } else {
                $validate['success'] = true;
            }

            return json_encode($validate);
        }

        if (session('logged_in')==false) {
            return redirect()->to('login');
        }

        $data['title'] = 'Add Customer';
        $data['page_title'] = 'Add a customer';
        $data['uri'] = service('uri');
        $data['custom_js'] = 'customer_vt/form.js';

        return view('customers_vt/add_customervt',$data);
    }

    public function edit_customervt($id) {

        $customerVtModel = new CustomersVtModel();

        if ($this->request->getMethod()=='post') {
            $validate = [
                "success" => false,
                "messages" => ''
            ];
    
            $data = [
                "customer_type" => $this->request->getPost('customer_type'),
                "customer_name" => $this->request->getPost('customer_name'),
                "contact_person" => $this->request->getPost('contact_person'),
                "notes" => $this->request->getPost('notes'),
                "contact_number" => $this->request->getPost('contact_number'),
                "email_address" => $this->request->getPost('email_address'),
                "source" => $this->request->getPost('source'),
                "address_province" => $this->request->getPost('address_province'),
                "address_city" => $this->request->getPost('address_city'),
                "address_brgy" => $this->request->getPost('address_brgy'),
                "address_sub" => $this->request->getPost('address_sub')
            ];
    
            if (!$customerVtModel->update($id, $data)) {
                $validate['messages'] = $customerVtModel->errors();
            } else {
                $validate['success'] = true;
            }
    
            return json_encode($validate);
        }

        // $data['customerVt'] = $customerVtModel->find($id);

        return json_encode($customerVtModel->find($id));

    }

    public function add_customervtbranch() {

        $customersVtBranchModel = new CustomersVtBranchModel();
        $customerVtModel = new CustomersVtModel();

        if ($this->request->getMethod() == 'post') {
            
            $validate = [
                "success" => false,
                "messages" => ''
            ];

            $data = [
                "customer_id" => $this->request->getPost("customer_id"),
                "branch_name" => $this->request->getPost("branch_name"),
                "address_province" => $this->request->getPost("address_province"),
                "address_city" => $this->request->getPost("address_city"),
                "address_brgy" => $this->request->getPost("address_brgy"),
                "address_sub" => $this->request->getPost("address_sub"),
                "contact_number" => $this->request->getPost("contact_number"),
                "contact_person" => $this->request->getPost("contact_person"),
                "email_address" => $this->request->getPost("email_address"),
                "notes" => $this->request->getPost("notes"),
            ];

            if (!$customersVtBranchModel->insert($data)) {
                $validate['messages'] = $customersVtBranchModel->errors();
            } else {
                $validate['success'] = true;
            }

            return json_encode($validate);
        }

        if (session('logged_in')==false) {
            return redirect()->to('login');
        }

        $data['title'] = 'Add Branch Customer';
        $data['page_title'] = 'Add a branch customer';
        $data['uri'] = service('uri');
        $data['customersvt'] = $customerVtModel->find();

        return view('customers_vt/add_customervtbranch',$data);
    }

    public function edit_customervtbranch($id) {

        $customersVtModel = new CustomersVtModel();
        $customersVtBranchModel = new CustomersVtBranchModel();

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
    
            if (!$customersVtBranchModel->update($id,$data)) {
                $validate['messages'] = $customersVtBranchModel->errors();
            } else {
                $validate['success'] = true;
            }
    
            return json_encode($validate);
        }

        if (session('logged_in')==false) {
            return redirect()->to('login');
        }


        $data['title'] = 'Customers';
        $data['page_title'] = 'Customers Menu';
        $data['customersvt'] = $customersVtModel->find();
        $data['customervtBranch'] = $customersVtBranchModel->find($id);
        $data['uri'] = service('uri');
        $data['id'] = $id;

        return view('customers_vt/add_customervtbranch',$data);

    }

    
    public function delete_customervt($id) {


        $customersvtModel = new CustomersVtModel();
        $validate = [
            "success" => false,
            "messages" => 'Customer has been deleted!'
        ];

        if (!$customersvtModel->delete($id)) {
            $validate['message'] = $customersvtModel->errors();
        } else {
            $validate['success'] = true;
        }

        return json_encode($validate);

    }

    public function delete_customervt_branch($id) {
        
        if (session('logged_in')==false) {
            return redirect()->to('login');
        }

        $customersvtBranchModel = new CustomersVtBranchModel();

        $data['title'] = 'Delete Branch Customer';
        $data['page_title'] = 'Delete Branch Customer';
        $data['uri'] = service('uri');
        $data['href'] = site_url('customervt-list');
        $customersvtBranchModel->delete($id);

        return view('templates/deletepage',$data);
    }

    public function customervt_list() {

        if (session('logged_in')==false) {
            return redirect()->to('login');
        }

        $data['title'] = 'Customers List';
        $data['page_title'] = 'Customers List';
        $data['custom_js'] = 'customer_vt/list.js';
        $data['with_dtTable'] = true;
        $data['with_jszip'] = true;
        $data['uri'] = service('uri');

        return view('customers_vt/customervt_table',$data);
    }


    public function getCustomersList() {
        $customersVtModel = new CustomersVtModel();
        $customersVtTable = new TablesIgniter();
        $customer_type = $this->request->getGet('customer_type');

        $customersVtTable->setTable($customersVtModel->noticeTable($customer_type))
                         ->setDefaultOrder("id","DESC")
                         ->setSearch([
                            "id",
                            "customer_type",
                            "customer_name",
                            "contact_person",
                            "address",
                            "contact_number",
                            "email_address",
                            "source",
                            "notes",
                         ])
                         ->setOrder([
                            "id",
                            null,
                            "customer_type",
                            "customer_name",
                            "contact_person",
                            "address",
                            "contact_number",
                            "email_address",
                            "source",
                            "notes",
                         ])
                         ->setOutput([
                            "id",
                            $customersVtModel->buttonEdit(),
                            "customer_type",
                            "customer_name",
                            "contact_person",
                            "address",
                            "contact_number",
                            "email_address",
                            "source",
                            "notes"
                         ]);
        
        return $customersVtTable->getDatatable();
    }

}
