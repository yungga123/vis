<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerBranchModel;
use App\Models\CustomersVtBranchModel;
use App\Models\CustomersVtBranchViewModel;
use App\Models\CustomersVtModel;
use App\Models\CustomersVtViewModel;

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

        if (session('logged_in')==false) {
            return redirect()->to('login');
        }

        $data['title'] = 'Edit Customer';
        $data['page_title'] = 'Edit a customer';
        $data['uri'] = service('uri');
        $data['id'] = $id;
        $data['customerVt'] = $customerVtModel->find($id);

        return view('customers_vt/add_customervt',$data);

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

    public function customervt_list() {

        if (session('logged_in')==false) {
            return redirect()->to('login');
        }

        $customersVtViewModel = new CustomersVtViewModel();
        $customerVtBranchViewModel = new CustomersVtBranchViewModel();
        $request = service('request');
        $searchData = $request->getGet();

        $data['title'] = 'List of Customers';
        $data['page_title'] = 'List of Customers';
        $data['uri'] = service('uri');
        $data['customerVtBranchViewModel'] = $customerVtBranchViewModel;

        $data['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
        $data['perPage'] = 10;
        $data['total'] = $customersVtViewModel->countAll();
        $search = "";
        if (isset($searchData) && isset($searchData['search'])) {
            $search = $searchData['search'];
        }

        if ($search == '') {
            $paginateData = $customersVtViewModel->orderBy('id', 'desc')->paginate($data['perPage']);
        } else {
            $paginateData = $customersVtViewModel->orLike('customer_name', $search)
                ->orLike('contact_person', $search)
                ->orLike('address', $search)
                ->orLike('contact_number', $search)
                ->orLike('email_address', $search)
                ->orLike('source', $search)
                ->orLike('notes', $search)
                ->orderBy('id', 'desc')
                ->paginate($data['perPage']);
        }

        $data['customersVtViewModel'] = $paginateData;
        $data['pager'] = $customersVtViewModel->pager;
        $data['search'] = $search;

        return view('customers_vt/customervt_table',$data);
    }

    public function delete_customervt($id) {

        if (session('logged_in')==false) {
            return redirect()->to('login');
        }

        $customersvtModel = new CustomersVtModel();

        $data['title'] = 'Delete Customer';
        $data['page_title'] = 'Delete Customer';
        $data['uri'] = service('uri');
        $data['href'] = site_url('customervt-list');
        $customersvtModel->delete($id);

        return view('templates/deletepage',$data);
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
}