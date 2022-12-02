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

        $taskleadModel = new TaskLeadModel();
        $customerModel = new CustomersModel();
        $customerFind = $customerModel->find(1);
        var_dump($customerFind['customer_name']);
    }
}
