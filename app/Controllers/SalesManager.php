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

    // Data
    public function taskleads() 
    {
        $model = $this->_model;

        $quarter = $this->request->getVar('quarter');

        $data['booked'] = count($model->where('quarter',$quarter)->where('status',100.00)->find());
        $data['negotiation'] = count($model->where('quarter',$quarter)->where('status',90.00)->find());
        $data['evaluation'] = count($model->where('quarter',$quarter)->where('status',70.00)->find());
        $data['dev_sol'] = count($model->where('quarter',$quarter)->where('status',50.00)->find());
        $data['qualified'] = count($model->where('quarter',$quarter)->where('status',30.00)->find());
        $data['identified'] = count($model->where('quarter',$quarter)->where('status',10.00)->find());

        return $this->response->setJSON($data);

    }

    
}
