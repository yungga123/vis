<?php

namespace App\Controllers\Clients;

use App\Controllers\BaseController;
use App\Models\CustomerBranchModel;
use App\Traits\ExportTrait;
use App\Traits\HRTrait;
use monken\TablesIgniter;

class CustomerBranch extends BaseController
{
    /* Declare trait here to use */
    use ExportTrait, HRTrait;

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
     * Use to check if can add
     * @var bool
     */
    private $_can_add;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_model       = new CustomerBranchModel(); // Current model
        $this->_module_code = MODULE_CODES['customers']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, ACTION_ADD);
    }

    /**
     * Get list of customers branch
     *
     * @return array|dataTable
     */
    public function list() 
    {
        $table          = new TablesIgniter();
        $customer_id    = $this->request->getVar('c');
        $builder        = $this->_model->noticeTable($customer_id);
        $fields         = [
            'branch_name',
            'contact_person',
            'contact_number',
            'address',
            'email_address',
            'notes',
            'created_by',
            'created_at'
        ];

        $table
            ->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.branch_name",
                "{$this->_model->table}.contact_person",
                "{$this->_model->table}.contact_number",
                "{$this->_model->table}.province",
                "{$this->_model->table}.city",
                "{$this->_model->table}.barangay",
                "{$this->_model->table}.subdivision",
                "{$this->_model->table}.email_address", 
            ])
            ->setDefaultOrder('id','desc')
            ->setOrder(array_merge([null], $fields))
            ->setOutput(
                array_merge(
                    [$this->_model->buttons($this->_permissions)], 
                    $fields
                )
            );

        return $table->getDatatable();
    }

    /**
     * For saving data
     *
     * @return json
     */
    public function save() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.added', 'Client Branch')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $action = ACTION_ADD;
    
                if ($this->request->getVar('id')) {
                    $action             = ACTION_EDIT;
                    $data['message']    = res_lang('success.updated', 'Client Branch');
                }

                $this->checkRoleActionPermissions($this->_module_code, $action, true);
                if (! $this->_model->save($this->request->getVar())) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
    
                    $errors = $this->_model->errors();
                    $arr = [];
                    if (! empty($errors)) {
                        foreach ($errors as $key => $value) {
                            $arr['b'.$key] = $value;
                        }
                    }
    
                    $data['errors']  = $arr;
                }
                return $data;
            },
            true
        );

        return $response;
    }

    /**
     * Fetch record
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved', 'Client Branch')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $branches       = $this->_model->select($this->_model->columns())->find($id);
                $data['data']   = $branches;
                return $data;
            },
            true
        );

        return $response;
    }

     /**
     * Deleting record
     *
     * @return json
     */
    public function delete() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.deleted', 'Client Branch')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);

                if (! $this->_model->delete($this->request->getVar('id'))) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                }
                return $data;
            }
        );

        return $response;
    }
}
