<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomersVtBranchModel;
use App\Models\CustomersVtModel;
use monken\TablesIgniter;

class CustomersVt extends BaseController
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
        $this->_module_code = MODULE_CODES['customers_commercial']; // Current module
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

        $data['title']          = 'Clients (Commercial)';
        $data['page_title']     = 'Clients | List (Commercial)';
        $data['can_add']        = $this->_can_add;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['custom_js']      = 'customersvt/list.js';
        $data['btn_add_lbl']    = 'Add New Client';

        return view('customers_vt/index', $data);
    }

    /**
     * Get list of customers
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table  = new TablesIgniter();
        $params = $this->request->getVar('params');
        $builder = $this->_model->noticeTable();

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
                "notes"
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
                "notes"
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
                "notes"
            ]);
        
        return $table->getDatatable();

    }

    /**
     * For saving data
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer has been saved successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            if (! $this->_model->save($this->request->getVar())) {
                $data['errors']     = $this->_model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            }

            if ($this->request->getVar('id')) {
                $data['message']    = 'Customer has been updated successfully!';
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception $e) {
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
            $id     = $this->request->getVar('id');

            $data['data'] = $this->_model->select($this->_model->allowedFields)->find($id);;
        } catch (\Exception$e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * Deleting record
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
            if (! $this->_model->delete($this->request->getVar('id'))) {
                $data['errors']     = $this->_model->errors();
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

    /**
     * Get list of customers branch
     *
     * @return array|dataTable
     */
    public function branchCustomervtList() 
    {
        $customersVtBranchModel = new CustomersVtBranchModel();
        $customersVtBranchTable = new TablesIgniter();
        $customervt_id = $this->request->getGet('customervt_id');

        $customersVtBranchTable
            ->setTable($customersVtBranchModel->noticeTable($customervt_id))
            ->setSearch([
                "branch_name",
                "contact_person",
                "contact_number",
                "address",
                "email_address",
                "notes"
            ])
            ->setDefaultOrder('id','desc')
            ->setOrder([
                null,
                "branch_name",
                "contact_person",
                "contact_number",
                "address",
                "email_address",
                "notes"
            ])
            ->setOutput([
                $customersVtBranchModel->buttons($this->_permissions),
                "branch_name",
                "contact_person",
                "contact_number",
                "address",
                "email_address",
                "notes"
            ]);

        return $customersVtBranchTable->getDatatable();
    }

    /**
     * For saving data
     *
     * @return json
     */
    public function saveBranch() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer Branch has been saved successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = new CustomersVtBranchModel();

            if (! $model->save($this->request->getVar())) {
                $data['errors']     = $model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";

                $errors = $model->errors();
                $arr = [];
                if (! empty($errors)) {
                    foreach ($errors as $key => $value) {
                        $arr['b'.$key] = $value;
                    }
                }

                $data['errors']  = $arr;
            }

            if ($this->request->getVar('id')) {
                $data['message']    = 'Customer Branch has been updated successfully!';
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception $e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * Deleting record
     *
     * @return json
     */
    public function editBranch() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer has been retrieved!'
        ];

        try {
            $model  = new CustomersVtBranchModel();
            $id     = $this->request->getVar('id');
            $branch = $model->select($model->allowedFields)->find($id);

            $data['data'] = $branch + $this->_model->getCustomerName($branch['customer_id']);
        } catch (\Exception$e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

     /**
     * Deleting record
     *
     * @return json
     */
    public function deleteBranch() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer Branch has been deleted successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = new CustomersVtBranchModel();

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
