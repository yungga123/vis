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
        var_dump(res_lang('restrict.action.change'));
        var_dump(res_lang('success.uploaded'));
    }
}
