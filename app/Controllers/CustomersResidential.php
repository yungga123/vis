<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomersVtModel;
use monken\TablesIgniter;

class CustomersResidential extends BaseController
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
    public function __construct()
    {
        $this->_model       = new CustomersVtModel(); // Current model
        $this->_module_code = MODULE_CODES['customers_residential']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
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

        $data['title']          = 'Clients (Residential)';
        $data['page_title']     = 'Clients | List (Residential)';
        $data['can_add']        = $this->_can_add;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['custom_js']      = 'customers_residential/list.js';
        $data['btn_add_lbl']    = 'Add New Client';

        return view('customers_residential/index', $data);
    }

    /**
     * Get list of customers
     *
     * @return array|dataTable
     */
    public function list() {
        $table = new TablesIgniter();
        $params = $this->request->getVar('params');
        $builder = $this->_model->noticeTable()->where('customer_type','Residential');;

        if ($params && $params['filter'] !== 'all') {
            $builder->where('forecast', $params['filter']);
        }

        $table
            ->setTable($builder)
            ->setSearch([
                "forecast",
                "id",
                "customer_name",
                "contact_person",
                "address",
                "contact_number",
                "email_address",
                "source",
                "notes",
                "referred_by"
            ])
            ->setDefaultOrder('id','desc')
            ->setOrder([
                null,
                "forecast",
                "id",
                "customer_name",
                "contact_person",
                "address",
                "contact_number",
                "email_address",
                "source",
                "notes",
                "referred_by"
            ])
            ->setOutput([
                $this->_model->buttons($this->_permissions),
                "forecast",
                "id",
                "customer_name",
                "contact_person",
                "address",
                "contact_number",
                "email_address",
                "source",
                "notes",
                "referred_by"
            ]);
        
        return $table->getDatatable();
    }

    public function save() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer has been saved successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = $this->_model;

            if (! $model->save($this->request->getVar())) {
                $data['errors']     = $model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            }

            if ($this->request->getVar('id')) {
                $data['message']    = 'Customer has been updated successfully!';
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * For getting the item data using the id
     *
     * @return json
     */
    public function edit() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer has been retrieved!'
        ];

        try {
            $model  = $this->_model;
            $id     = $this->request->getVar('id');
            // $item   = $model->select($model->allowedFields)->find($id);

            $data['data'] = $model->select($model->allowedFields)->find($id);;
        } catch (\Exception$e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * Saving process of items
     *
     * @return json
     */
    public function delete() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer has been deleted successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = $this->_model;

            if (! $model->delete($this->request->getVar('id'))) {
                $data['errors']     = $model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }
}
