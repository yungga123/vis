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
        $this->_model       = new CustomerBranchModel(); // Current model
        $this->_module_code = MODULE_CODES['customers']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
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

        $table
            ->setTable($builder)
            ->setSearch([
                'branch_name',
                'contact_person',
                'contact_number',
                'province',
                'city',
                'barangay',
                'subdivision',
                'email_address', 
            ])
            ->setDefaultOrder('id','desc')
            ->setOrder([
                null,
                'branch_name',
                'contact_person',
                'contact_number',
                'address',
                'email_address',
                'notes',
                'created_by',
                'created_at'
            ])
            ->setOutput([
                $this->_model->buttons($this->_permissions),
                'branch_name',
                'contact_person',
                'contact_number',
                'address',
                'email_address',
                'notes',
                'created_by',
                'created_at'
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
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer Branch has been saved successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                if (! $this->_model->save($this->request->getVar())) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
    
                    $errors = $this->_model->errors();
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer branches have been retrieved!'
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer Branch has been deleted successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                if (! $this->_model->delete($this->request->getVar('id'))) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                }
                return $data;
            }
        );

        return $response;
    }

    /**
     * For exporting data to csv
     *
     * @return void
     */
    public function export() 
    {
        $datetimeFormat = dt_sql_datetime_format();
        $address    = dt_sql_concat_client_address();
        $columns    = "
            {$this->_model->table}.id,
            {$this->_model->table}.customer_id,
            {$this->_model->table}.branch_name,
            {$this->_model->table}.contact_person,
            {$this->_model->table}.contact_number,
            {$this->_model->table}.email_address,
            {$address},
            {$this->_model->table}.notes,
            DATE_FORMAT({$this->_model->table}.created_at, '{$datetimeFormat}') AS created_at,
            cb.employee_name AS created_by
        ";
        $builder    = $this->_model->select($columns);
        $this->joinAccountView($builder, "{$this->_model->table}.created_by", 'cb');
        $builder->where("deleted_at IS NULL")->orderBy('id', 'DESC');

        $data       = $builder->findAll();
        $header     = [
            'Branch ID',
            'Client ID',
            'Client Branch Name',
            'Contact Person',
            'Contact Number',
            'Email Address',
            'Address',
            'Notes',
            'Created By',
            'Created At',
        ];
        $filename   = 'Client Branches Masterlist';

        $this->exportToCsv($data, $header, $filename);
    }
}
