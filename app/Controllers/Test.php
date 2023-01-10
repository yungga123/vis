<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomersModel;
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

        helper('text');

        return random_string('crypto');
        
        
    }
}
