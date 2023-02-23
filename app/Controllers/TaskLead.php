<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomersModel;
use App\Models\CustomersResidentialModel;
use App\Models\CustomersVtModel;
use App\Models\TaskLeadModel;
use CodeIgniter\I18n\Time;
use Exception;
use monken\TablesIgniter;

class Tasklead extends BaseController
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
     * Use to check if can add
     * @var bool
     */
    private $_can_add;

    /**
     * Class constructor
     */

     private $_time;

    /**
     * Class constructor
     */

    
    public function __construct()
    {
        $this->_time        = new Time();
        $this->_model       = new TaskLeadModel(); // Current model
        $this->_module_code = MODULE_CODES['task_lead']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
    }

    public function index()
    {
        $data['title']          = 'Task Lead';
        $data['page_title']     = 'Task Lead | List';
        $data['custom_js']      = 'tasklead/list.js';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['can_add']        = $this->_can_add;
        $data['quarter']        = $this->_time->getQuarter();

        return view('task_lead/index', $data);
    }

    public function list()
    {
        $table = new TablesIgniter();

        $table->setTable($this->_model->noticeTable())
            ->setSearch([
                "id",
                "employee_name",
                "quarter",
                "status",
                "status_percent",
                "customer_name",
                "branch_name",
                "contact_number",
                "project",
                "project_amount",
                "quotation_num",
                "forecast_close_date",
                "min_forecast_date",
                "max_forecast_date",
                "status1",
                "remark_next_step",
                "close_deal_date",
                "project_start_date",
                "project_finish_date",
                "project_duration"
            ])
            ->setOrder([
                null,
                "id",
                "employee_name",
                "quarter",
                "status",
                "status_percent",
                "customer_name",
                "branch_name",
                "contact_number",
                "project",
                "project_amount",
                "quotation_num",
                "forecast_close_date",
                "min_forecast_date",
                "max_forecast_date",
                "status1",
                "remark_next_step",
                "close_deal_date",
                "project_start_date",
                "project_finish_date",
                "project_duration"
            ])
            ->setOutput([
                $this->_model->buttons($this->_permissions),
                "id",
                "employee_name",
                "quarter",
                "status",
                "status_percent",
                "customer_name",
                "branch_name",
                "contact_number",
                "project",
                "project_amount",
                "quotation_num",
                "forecast_close_date",
                "min_forecast_date",
                "max_forecast_date",
                "status1",
                "remark_next_step",
                "close_deal_date",
                "project_start_date",
                "project_finish_date",
                "project_duration"
            ]);

        return $table->getDatatable();
    }

     /**
     * Saving process of employees (inserting and updating employees)
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Employee has been added successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $id     = $this->request->getVar('id');
            $rules  = $this->_model->getValidationRules();

            $this->_model->setValidationRules($rules);

            if (! $this->_model->save($this->request->getVar())) {
                $data['errors']     = $this->_model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            }

            if ($this->request->getVar('id')) {
                $data['message']    = 'Tasklead has been updated successfully!';
            }

            // Commit transaction
            $this->transCommit();
        } catch (Exception $e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * For getting the employee data using the id
     *
     * @return json
     */
    public function edit() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Tasklead has been retrieved!'
        ];

        try {
            $id     = $this->request->getVar('id');
            $fields = $this->_model->allowedFields;

            $data['data'] = $this->_model->select($fields)->find($id);;
        } catch (\Exception$e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * Deletion of employee
     *
     * @return json
     */
    public function delete() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Tasklead has been deleted successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            if (! $this->_model->delete($this->request->getVar('id'))) {
                $data['errors']     = $this->_model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            }

            // Commit transaction
            $this->transCommit();
        } catch (Exception $e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }





    public function getVtCustomer() {
        $model = new CustomersVtModel();
        $data['data'] = $model->find();
        $data['success'] = true;

        return $this->response->setJSON($data);
    }

    public function getForecastCustomer() {
        $model = new CustomersModel();
        $data['data'] = $model->find();
        $data['success'] = true;

        return $this->response->setJSON($data);
    }

    public function getResidentialCustomers() {
        $model = new CustomersResidentialModel();
        $data['data'] = $model->find();
        $data['success'] = true;

        return $this->response->setJSON($data);
    }
}
