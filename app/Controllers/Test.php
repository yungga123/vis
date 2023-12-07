<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Test extends BaseController
{
    public function index()
    {
        var_dump(res_lang('restrict.action.change'));
        var_dump(res_lang('success.uploaded'));
        d(get_array_duplicate([1, 2, 2, '3', 3, 4, 5]));
    }
}
