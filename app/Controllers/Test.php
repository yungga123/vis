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
        // foreach ($taskleadModel->noticeTable() as $row) {
        //     var_dump($row['tasklead_id']);
        // }
        var_dump($taskleadModel->noticeTable());
    }
}
