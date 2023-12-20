<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\TaskLeadModel;

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
     * @var array
     */
    private $_permissions;

    /**
     * Class constructor
     * 
     */
    public function __construct()
    {
        $this->_model       = new TaskLeadModel(); // Current model
        $this->_module_code = MODULE_CODES['manager_sales']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
    }

    /**
     * Display the view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code);

        $data['title']          = 'Manager of Sales';
        $data['page_title']     = 'Manager of Sales';
        $data['custom_js']      = 'sales/sales_manager/index.js';
        $data['custom_css']     = 'sales/sales_manager/index.css';
        $data['highcharts']     = true;
        $data['sweetalert2']    = true;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['routes']         = json_encode([
            'sales_manager' => [
                'taskleads'             => url_to('sales_manager.taskleads'),
                'taskleads_stats'       => url_to('sales_manager.taskleads_stats'),
                'taskleads_quarterly'   => url_to('sales_manager.taskleads_quarterly'),
                'target_list'           => url_to('sales_target.list'),
                'target_employees'      => url_to('sales_target.employees'),
                'target_employee'       => url_to('sales_target.employee'),
                'target_sales'          => url_to('sales_target.target_sales'),
                'target_delete'         => url_to('sales_target.delete'),
            ],
        ]);

        return view('sales/manager_of_sales/index', $data);
    }

    // Data for PIE CHARTS
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

    // Data for Over-All Stats
    public function taskleads_stats()
    {
        $model = $this->_model;

        $data['booked'] = count($model->where('status',100.00)->find());
        $data['negotiation'] = count($model->where('status',90.00)->find());
        $data['evaluation'] = count($model->where('status',70.00)->find());
        $data['dev_sol'] = count($model->where('status',50.00)->find());
        $data['qualified'] = count($model->where('status',30.00)->find());
        $data['identified'] = count($model->where('status',10.00)->find());

        $data['booked_amt'] = $model->selectSum('project_amount')->where('status',100.00)->find();
        $data['negotiation_amt'] = $model->selectSum('project_amount')->where('status',90.00)->find();
        $data['evaluation_amt'] = $model->selectSum('project_amount')->where('status',70.00)->find();
        $data['dev_sol_amt'] = $model->selectSum('project_amount')->where('status',50.00)->find();
        $data['qualified_amt'] = $model->selectSum('project_amount')->where('status',30.00)->find();
        $data['identified_amt'] = $model->selectSum('project_amount')->where('status',10.00)->find();

        return $this->response->setJSON($data);
    }

    // Data for Quarterly Stats
    public function taskleads_quarterly()
    {
        $model = $this->_model;

        $quarter = $this->request->getVar('quarter');

        $data['booked'] = $model->where('quarter',$quarter)->where('status',100.00)->find();
        $data['negotiation'] = $model->where('quarter',$quarter)->where('status',90.00)->find();
        $data['evaluation'] = $model->where('quarter',$quarter)->where('status',70.00)->find();
        $data['dev_sol'] = $model->where('quarter',$quarter)->where('status',50.00)->find();
        $data['qualified'] = $model->where('quarter',$quarter)->where('status',30.00)->find();
        $data['identified'] = $model->where('quarter',$quarter)->where('status',10.00)->find();

        $data['booked_amt'] = $model->selectSum('project_amount')->where('quarter',$quarter)->where('status',100.00)->find();
        $data['status1'] = $model->select("IF(close_deal_date<DATE_ADD(forecast_close_date, INTERVAL 6 DAY) AND close_deal_date>DATE_SUB(forecast_close_date, INTERVAL 6 DAY),'HIT','MISSED') as status1")->where('quarter',$quarter)->where('status',100.00)->find();

        return $this->response->setJSON($data);

    }

    
    
}
