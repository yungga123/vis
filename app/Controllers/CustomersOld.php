<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\DataTable;
use App\Models\CustomerBranchModel;
use App\Models\CustomerBranchViewModel;
use App\Models\CustomersModel;
use App\Models\CustomersViewModel;
use CodeIgniter\API\ResponseTrait;
use monken\TablesIgniter;

class Customers extends BaseController
{

    use ResponseTrait;

    public function index()
    {

        if (session('logged_in') == true) {

            $data['title'] = 'Add Customer';
            $data['page_title'] = 'Add Customer';
            $data['uri'] = service('uri');

            return view('customers/add_customer',$data);

        } else {
            return redirect()->to('login');
        }
    }

    public function add_customers()
    {
        $customersModel = new CustomersModel();

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

        if (!$customersModel->insert($data)) {
            $validate['messages'] = $customersModel->errors();
        } else {
            $validate['success'] = true;
        }

        echo json_encode($validate);
    }

    public function customer_table()
    {


        if (session('logged_in') == true) {


            $data['title'] = 'List of Customers';
            $data['uri'] = service('uri');
            echo view('templates/header', $data);
            echo view('customers/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('customers/customer_table');
            echo view('templates/footer');
            echo view('customers/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function getCustomers()
    {

        $customersModel = new CustomersModel();
        $customersTable = new TablesIgniter();

        $customersTable->setTable($customersModel->noticeTable())
            ->setDefaultOrder('id', 'DESC')
            ->setSearch([
                "id",
                "customer_name",
                "contact_person",
                "address",
                "contact_number",
                "email_address",
                "source",
                "notes"
            ])
            ->setOrder([
                "id",
                null,
                "customer_name",
                "contact_person",
                "address",
                "contact_number",
                "email_address",
                "source",
                "notes"
            ])
            ->setOutput([
                "id",
                $customersModel->buttonEdit(),
                "customer_name",
                "contact_person",
                "address",
                "email_address",
                "contact_number",
                "source",
                "notes"
            ]);

        return $customersTable->getDatatable();
    }


    public function customers_list()
    {
        if (session('logged_in') == false) {
            return redirect()->to('login');
        }

        $customersViewModel = new CustomersViewModel();
        $customerBranchViewModel = new CustomerBranchViewModel();
        $request = service('request');
        $searchData = $request->getGet();

        $data['title'] = 'List of Customers';
        $data['uri'] = service('uri');
        $data['customerBranchViewModel'] = $customerBranchViewModel;

        $data['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
        $data['perPage'] = 10;
        $data['total'] = $customersViewModel->countAll();
        $search = "";
        if (isset($searchData) && isset($searchData['search'])) {
            $search = $searchData['search'];
        }

        if ($search == '') {
            $paginateData = $customersViewModel->orderBy('id', 'desc')->paginate($data['perPage']);
        } else {
            $paginateData = $customersViewModel->orLike('customer_name', $search)
                ->orLike('contact_person', $search)
                ->orLike('address', $search)
                ->orLike('contact_number', $search)
                ->orLike('email_address', $search)
                ->orLike('source', $search)
                ->orLike('notes', $search)
                ->orderBy('id', 'desc')
                ->paginate($data['perPage']);
        }

        $data['customersViewModel'] = $paginateData;
        $data['pager'] = $customersViewModel->pager;
        $data['search'] = $search;
        $data['page_title'] = 'Customers Forecast List';

        return view('customers/customer_table_branch',$data);
    }

    public function edit_customers($id)
    {
        if (session('logged_in') == true) {

            $customersModel = new CustomersModel();

            $data['title'] = 'Update a customer';
            $data['page_title'] = 'Update Customer';
            $data['customer_details'] = $customersModel->find($id);
            $data['id'] = $id;
            $data['uri'] = service('uri');

            return view('customers/add_customer',$data);
        } else {
            return redirect()->to('login');
        }
    }

    public function edit_customers_validate()
    {
        $customersModel = new CustomersModel();

        $validate = [
            "success" => false,
            "messages" => ''
        ];

        $id = $this->request->getPost('id');
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

        if (!$customersModel->update($id, $data)) {
            $validate['messages'] = $customersModel->errors();
        } else {
            $validate['success'] = true;
        }

        echo json_encode($validate);
    }

    public function delete_customer($id)
    {
        if (session('logged_in') == true) {

            $customersModel = new CustomersModel();

            $data['title'] = 'Delete Customer';
            $data['page_title'] = 'Delete Customer';
            $data['uri'] = service('uri');
            $data['href'] = site_url('customers-list');
            $customersModel->delete($id);

            echo view('templates/header', $data);
            echo view('customers/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('templates/deletepage');
            echo view('templates/footer');
            echo view('customers/script');
        } else {
            return redirect()->to('login');
        }
    }
}
