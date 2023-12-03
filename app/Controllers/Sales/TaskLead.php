<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\CustomerBranchModel;
use App\Models\CustomerModel;
use App\Models\TaskleadHistoryModel;
use App\Models\TaskLeadModel;
use CodeIgniter\I18n\Time;
use Exception;
use monken\TablesIgniter;

class Tasklead extends BaseController
{
    /* Declare trait here to use */

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
     * 
     * @var
     */
     private $_time;

    /**
     * 
     * @var
     */
    private $_taskleadHistoryModel;

    /**
     * Class constructor
     * 
     */
    public function __construct()
    {
        $this->_time        = new Time();
        $this->_model       = new TaskLeadModel(); // Current model
        $this->_module_code = MODULE_CODES['task_lead']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
        $this->_taskleadHistoryModel = new TaskleadHistoryModel();
    }

    /**
     * Display the view
     *
     * @return view
     */
    public function index()
    {
        $data['title']          = 'Task Lead';
        $data['page_title']     = 'Task Lead | List';
        $data['btn_add_lbl']    = 'Add New Tasklead';
        $data['can_add']        = $this->_can_add;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['select2']        = true;
        $data['quarter']        = $this->_time->getQuarter();
        $data['custom_js']      = ['sales/tasklead/index.js', 'dt_filter.js'];
        $data['routes']         = json_encode([
            'tasklead' => [
                'list'      => url_to('tasklead.list'),
                'edit'      => url_to('tasklead.edit'),
                'delete'    => url_to('tasklead.delete'),
                'customer_commercial'   => url_to('tasklead.getcustomervt'),
                'customer_branch'       => url_to('tasklead.getcustomervtbranch'),
                'customer_residential'  => url_to('tasklead.getcustomerresidential'),
            ],
        ]);

        // get initials for the name (used for quotation)
        $words = explode(' ', session('name'));
        $inits = '';
        foreach($words as $word){
            $inits .= strtoupper(substr($word,0,1));
        }
        
        $quotation_num = $inits . date('ym');
        $data['quotation_num'] = $quotation_num;

        return view('sales/task_lead/index', $data);
    }

    /**
     * Get list of records
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table      = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request);
        $fields     = $this->_model->dtColumns;

        $table->setTable($builder)
            ->setSearch([
                'id',
                'employee_name',
                'customer_name',
                'project',
                'project_amount',
                'quotation_num',
                'status1',
                'remark_next_step',
            ])
            ->setDefaultOrder('id','desc')
            ->setOrder(array_merge([null, null], $fields))
            ->setOutput(
                array_merge(
                    [dt_empty_col(), $this->_model->buttons($this->_permissions)], 
                    $fields
                )
            );

        return $table->getDatatable();
    }

     /**
     * Saving process of record (inserting and updating record)
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

            if (! empty($id)) {                
                $data['message']    = 'Tasklead has been updated successfully!';                
            } else $id = $this->_model->getInsertID();


            //update customer to existing customer if customer is forecast after booked
            if ($this->request->getVar('existing_customer')==0 && $this->request->getVar('status')=='100.00' && $this->request->getVar('customer_type')=='Commercial') {
                $customersVtModel = new CustomerModel();

                $customersVtModel->update(
                    $this->request->getVar('customer_id'),
                    [
                        'forecast' => 0
                    ]
                );
            } elseif(($this->request->getVar('existing_customer')==0 && $this->request->getVar('status')=='100.00' && $this->request->getVar('customer_type')=='Residential')) {

                $customersResidentialModel = new CustomerModel();

                $customersResidentialModel->update(
                    $this->request->getVar('customer_id'),
                    [
                        'forecast' => 0
                    ]
                );
            }

            
            $this->_taskleadHistoryModel->insert([
                'tasklead_id' => $id,
                'quarter' => $this->request->getVar('quarter'),
                'status' => $this->request->getVar('status'),
                'customer_id' => $this->request->getVar('customer_id'),
                'project' => $this->request->getVar('project'),
                'project_amount' => $this->request->getVar('project_amount'),
                'quotation_num' => $this->request->getVar('quotation_num'),
                'forecast_close_date' => $this->request->getVar('forecast_close_date'),
                'remark_next_step' => $this->request->getVar('remark_next_step'),
                'close_deal_date' => $this->request->getVar('close_deal_date'),
                'project_start_date' => $this->request->getVar('project_start_date'),
                'project_finish_date' => $this->request->getVar('project_finish_date')
            ]);
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
     * For getting the data using the id
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
     * Deletion of record
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

    public function getVtCustomer() 
    {
        $model = new CustomerModel();
        $forecast = $this->request->getVar('forecast');
        $data['data'] = $model->where('forecast', $forecast)->where('type','COMMERCIAL')->find();
        $data['success'] = true;

        return $this->response->setJSON($data);
    }

    public function getResidentialCustomers() 
    {
        $model = new CustomerModel();
        $forecast = $this->request->getVar('forecast');
        $data['data'] = $model->where('forecast', $forecast)->where('type','RESIDENTIAL')->find();
        $data['success'] = true;

        return $this->response->setJSON($data);
    }

    public function getCustomerVtBranch() 
    {
        $model = new CustomerBranchModel();
        $id = $this->request->getVar('id');
        $data['data'] = $model->where('customer_id',$id)->find();
        $data['success'] = true;

        return $this->response->setJSON($data);
    }
}
