<?php

namespace App\Controllers\Purchasing;

use App\Controllers\BaseController;
use App\Models\SupplierBrandsModel;
use monken\TablesIgniter;

class SupplierBrands extends BaseController
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
     * Class constructor
     */
    public function __construct()
    {
        $this->_model       = new SupplierBrandsModel(); // Current model
        $this->_module_code = MODULE_CODES['suppliers']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
    }

    /**
     * Get list of records
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table          = new TablesIgniter();
        $supplier_id    = $this->request->getVar('supplier_id');
        $builder        = $this->_model->noticeTable($supplier_id);
        $fields         = [
            'brand_name',
            'product',
            'warranty',
            'sales_person',
            'sales_contact_number',
            'technical_support',
            'technical_contact_number',
            'remarks',
            'created_by',
            'created_at',
        ];

        $table->setTable($builder)
            ->setSearch($fields)
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
     * Saving process of suppliers (inserting and updating suppliers)
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.saved', 'Supplier\'s brand')
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = $this->_model;

            if (! $model->save($this->request->getVar())) {
                $data['errors']     = $model->errors();
                $data['status']     = res_lang('status.error');
                $data['message']    = res_lang('error.validation');
            }

            if ($this->request->getVar('id')) {
                $data['message']    = res_lang('success.updated', 'Supplier\'s brand');
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();
            $this->logExceptionError($e, __METHOD__);

            $data['status']     = res_lang('status.error');
            $data['message']    = res_lang('error.process');
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved', 'Supplier\'s brand')
        ];

        try {
            $model  = $this->_model;
            $id     = $this->request->getVar('id');

            $data['data'] = $model->select($model->allowedFields)->find($id);;
        } catch (\Exception$e) {
            $this->logExceptionError($e, __METHOD__);
            $data['status']     = res_lang('status.error');
            $data['message']    = res_lang('error.process');
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.deleted', 'Supplier\'s brand')
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = $this->_model;

            if (! $model->delete($this->request->getVar('id'))) {
                $data['errors']     = $model->errors();
                $data['status']     = res_lang('status.error');
                $data['message']    = res_lang('error.validation');
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();
            $this->logExceptionError($e, __METHOD__);
            
            $data['status']     = res_lang('status.error');
            $data['message']    = res_lang('error.process');
        }

        return $this->response->setJSON($data);
    }
}
