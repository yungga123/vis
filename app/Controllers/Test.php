<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomersModel;
use App\Models\CustomersVtModel;
use App\Models\TaskLeadModel;
use Kint\Zval\Value;

class Test extends BaseController
{
    public function index()
    {

        // $taskleadModel = new TaskLeadModel();
        // $customerModel = new CustomersModel();
        // $customerFind = $customerModel->find(1);
        // // d($taskleadModel->booked_projects());
        // // $db      = \Config\Database::connect();
        // // $query = $db->query('SELECT * FROM task_lead_booked');
        // $result = $taskleadModel->booked_projects()->getResult();

        // foreach ($result as $row) {
        //     echo $row['id'];
        // }

        // $db = \Config\Database::connect();
        // $query = $db->query("SELECT * FROM tasklead ORDER BY id DESC LIMIT 1");
        // $result = $query->getResultObject();

        // helper('text');

        // return random_string('crypto');\
        
        // $customersVtModel = new CustomersVtModel();

        // $customersVtLastData = $customersVtModel->orderBy('id','desc')->limit(1)->find();

        // $id = 10;
        // $taskleadModel = new TaskLeadModel();
        // $customersModel = new CustomersModel();

        // $taskleadData = $taskleadModel->find($id);
        // $customersData = $customersModel->find($taskleadData['customer_id']);

        $customersModel = new CustomersModel();
        $customersVtModel = new CustomersVtModel();
        $taskleadModel = new TaskLeadModel();
        
        $id = 23;
        $taskleadData = $taskleadModel->find($id);
        $customerID = $taskleadData['customer_id'];
        $customersData = $customersModel->find($customerID);
        $customersVTInsert = [
            "customer_name" => $customersData['customer_name'],
            "contact_person" => $customersData['contact_person'],
            "address_province" => $customersData['address_province'],
            "address_city" => $customersData['address_city'],
            "address_brgy" => $customersData['address_brgy'],
            "address_sub" => $customersData['address_sub'],
            "contact_number" => $customersData['contact_number'],
            "email_address" => $customersData['email_address'],
            "source" => $customersData['source'],
            "notes" => $customersData['notes'],
        ];
        
        if (!$customersVtModel->insert($customersVTInsert)) {
            return d($customersVtModel->errors());
        }

        echo "success";
        
        

        
        
    }
}
