<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\TaskLeadModel;

class SalesManagerIndividual extends BaseController
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

    /**
     * Use to get current session of employee ID
     * @var string
     */
    private $_session;

    /**
     * Class constructor
     * 
     */
    public function __construct()
    {
        $this->_model       = new TaskLeadModel(); // Current model
        $this->_module_code = MODULE_CODES['manager_sales_indv']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_session     = session('employee_id');
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
        $data['custom_js']      = 'sales/sales_manager_indv/index.js';
        $data['custom_css']     = 'sales/sales_manager_indv/index.css';
        $data['highcharts']     = true;
        $data['routes']         = json_encode([
            'sales_manager_indv' => [
                'taskleads'             => url_to('sales_manager_indv.taskleads'),
                'taskleads_stats'       => url_to('sales_manager_indv.taskleads_stats'),
                'taskleads_quarterly'   => url_to('sales_manager_indv.taskleads_quarterly'),
                'indv_sales_target'     => url_to('sales_target.indv_sales_target'),
            ],
        ]);

        return view('sales/manager_of_sales_indv/index', $data);
    }

    // Data for PIE CHARTS
    public function taskleads() 
    {
        $model = $this->_model;

        $quarter = $this->request->getVar('quarter');

        $data['booked'] = count($model->where('quarter',$quarter)->where('status',100.00)->where('employee_id',$this->_session)->find());
        $data['negotiation'] = count($model->where('quarter',$quarter)->where('status',90.00)->where('employee_id',$this->_session)->find());
        $data['evaluation'] = count($model->where('quarter',$quarter)->where('status',70.00)->where('employee_id',$this->_session)->find());
        $data['dev_sol'] = count($model->where('quarter',$quarter)->where('status',50.00)->where('employee_id',$this->_session)->find());
        $data['qualified'] = count($model->where('quarter',$quarter)->where('status',30.00)->where('employee_id',$this->_session)->find());
        $data['identified'] = count($model->where('quarter',$quarter)->where('status',10.00)->where('employee_id',$this->_session)->find());

        return $this->response->setJSON($data);

    }

    // Data for Over-All Stats
    public function taskleads_stats()
    {
        $model = $this->_model;

        $data['booked'] = count($model->where('status',100.00)->where('employee_id',$this->_session)->find());
        $data['negotiation'] = count($model->where('status',90.00)->where('employee_id',$this->_session)->find());
        $data['evaluation'] = count($model->where('status',70.00)->where('employee_id',$this->_session)->find());
        $data['dev_sol'] = count($model->where('status',50.00)->where('employee_id',$this->_session)->find());
        $data['qualified'] = count($model->where('status',30.00)->where('employee_id',$this->_session)->find());
        $data['identified'] = count($model->where('status',10.00)->where('employee_id',$this->_session)->find());

        $data['booked_amt'] = $model->selectSum('project_amount')->where('status',100.00)->where('employee_id',$this->_session)->find();
        $data['negotiation_amt'] = $model->selectSum('project_amount')->where('status',90.00)->where('employee_id',$this->_session)->find();
        $data['evaluation_amt'] = $model->selectSum('project_amount')->where('status',70.00)->where('employee_id',$this->_session)->find();
        $data['dev_sol_amt'] = $model->selectSum('project_amount')->where('status',50.00)->where('employee_id',$this->_session)->find();
        $data['qualified_amt'] = $model->selectSum('project_amount')->where('status',30.00)->where('employee_id',$this->_session)->find();
        $data['identified_amt'] = $model->selectSum('project_amount')->where('status',10.00)->where('employee_id',$this->_session)->find();

        return $this->response->setJSON($data);
    }

    // Data for Quarterly Stats
    public function taskleads_quarterly()
    {
        $model = $this->_model;

        $quarter = $this->request->getVar('quarter');

        $data['booked'] = $model->where('quarter',$quarter)->where('status',100.00)->where('employee_id',$this->_session)->find();
        $data['negotiation'] = $model->where('quarter',$quarter)->where('status',90.00)->where('employee_id',$this->_session)->find();
        $data['evaluation'] = $model->where('quarter',$quarter)->where('status',70.00)->where('employee_id',$this->_session)->find();
        $data['dev_sol'] = $model->where('quarter',$quarter)->where('status',50.00)->where('employee_id',$this->_session)->find();
        $data['qualified'] = $model->where('quarter',$quarter)->where('status',30.00)->where('employee_id',$this->_session)->find();
        $data['identified'] = $model->where('quarter',$quarter)->where('status',10.00)->where('employee_id',$this->_session)->find();

        $data['booked_amt'] = $model->selectSum('project_amount')->where('quarter',$quarter)->where('status',100.00)->where('employee_id',$this->_session)->find();
        $data['status1'] = $model->select("IF(close_deal_date<DATE_ADD(forecast_close_date, INTERVAL 6 DAY) AND close_deal_date>DATE_SUB(forecast_close_date, INTERVAL 6 DAY),'HIT','MISSED') as status1")->where('quarter',$quarter)->where('status',100.00)->where('employee_id',$this->_session)->find();

        return $this->response->setJSON($data);

    }
}
