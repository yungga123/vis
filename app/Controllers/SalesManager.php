<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TaskLeadModel;
use App\Models\TaskLeadView;

class SalesManager extends BaseController
{
    /**
     * Use to initialize PermissionModel class
     * @var object
     */
    private $_model;

    /**
     * Use to get current module code
     * @var string
     */
    private $_module_code;

    /**
     * Use to get current permissions
     * @var string
     */

     private $_permissions;

    public function __construct()
    {
        $this->_model       = new TaskLeadModel(); // Current model
        $this->_module_code = MODULE_CODES['manager_sales']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
    }

    public function index()
    {
        $data['title']          = 'Manager of Sales';
        $data['page_title']     = 'Manager of Sales';
        $data['custom_js']      = 'sales_manager/index.js';
        $data['custom_css']     = 'sales_manager/index.css';
        $data['highcharts']     = true;

        return view('manager_of_sales/index', $data);
    }
}
