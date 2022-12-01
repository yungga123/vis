<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Libraries\DataTable;
use App\Models\CustomersModel;
use CodeIgniter\API\ResponseTrait;
use monken\TablesIgniter;

class Customers extends BaseController
{

    use ResponseTrait;
    
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
		// $dataTable = new DataTable;
		// $response = $dataTable->process('CustomersModel', [
        //     [
		// 		'name' => 'id',
        //         'formatter' => 'customer_id_format',
		// 	],
		// 	[
		// 		'name' => 'customer_name'
		// 	],
		// 	[
		// 		'name' => 'contact_person'
		// 	],
		// 	[
		// 		'name' => 'address_province'
		// 	],
		// 	[
		// 		'name' => 'address_city'
		// 	],
		// 	[
		// 		'name' => 'address_brgy'
		// 	],
		// 	[
		// 		'name' => 'address_sub'
		// 	],
		// 	[
		// 		'name' => 'contact_number'
		// 	],
		// 	[
		// 		'name' => 'source'
		// 	],
		// 	[
		// 		'name' => 'notes'
		// 	]
		// ]);
		
		// return $this->setResponseFormat('json')->respond($response);

        $customersModel = new CustomersModel();
        $customersTable = new TablesIgniter();

        $customersTable->setTable($customersModel->noticeTable())
                       ->setDefaultOrder('id', 'DESC')
                       ->setOrder([
                            "id",
                            "customer_name",
                            "contact_person",
                            "address_province",
                            "address_city",
                            "address_brgy",
                            "address_sub",
                            "contact_number",
                            "email_address",
                            "source",
                            "notes"
                       ])
                       ->setOutput([
                            "id",
                            "customer_name",
                            "contact_person",
                            "address_province",
                            "address_city",
                            "address_brgy",
                            "address_sub",
                            "contact_number",
                            "email_address",
                            "source",
                            "notes"
                       ]);
        
        return $customersTable->getDatatable();

	}
}
