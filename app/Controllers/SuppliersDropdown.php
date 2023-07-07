<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SuppliersDropdownModel;

class SuppliersDropdown extends BaseController
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
        $this->_model       = new SuppliersDropdownModel(); // Current model
        $this->_module_code = MODULE_CODES['suppliers']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
    }

    public function index()
    {
        $data['title']          = 'Suppliers Dropdown';
        $data['page_title']     = 'Suppliers Dropdown';
        $data['custom_js']      = 'suppliers_dropdown/index.js';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['select2']        = true;
        
        //$data['can_add']        = $this->_can_add;
        //$data['btn_add_lbl']    = 'Add New Supplier';


        return view('suppliers_dropdown/index', $data);
    }

    /**
     * Saving process of suppliers (inserting and updating suppliers)
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Supplier Dropdown has been saved successfully!'
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
                $data['message']    = 'Supplier Dropdown has been updated successfully!';
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
     * For getting the supplier data using the id
     *
     * @return json
     */
    public function edit() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Supplier Dropdown has been retrieved!'
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
     * Delete process of suppliers
     *
     * @return json
     */
    public function delete() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Supplier Dropdown has been deleted successfully!'
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

    public function fetchDropdowns() {
        $data['dropdowns'] = $this->_model->where('dropdown_type','Suppliers')->find();

        return $this->response->setJSON($data);
    }
}
