<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SupplierBrandsModel;
use monken\TablesIgniter;

class SupplierBrands extends BaseController
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
        $this->_model       = new SupplierBrandsModel(); // Current model
        $this->_module_code = MODULE_CODES['suppliers']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
    }


    public function list()
    {
        $table = new TablesIgniter();

        $supplier_id = $this->request->getVar('supplier_id');
        $builder = $this->_model->noticeTable($supplier_id);

        $table->setTable($builder)
            ->setSearch([
                "brand_name",
                "product",
                "warranty",
                "sales_person",
                "sales_contact_number",
                "technical_support",
                "technical_contact_number",
                "supplier_brands_remark"
            ])
            ->setDefaultOrder('id','desc')
            ->setOrder([
                null,
                "brand_name",
                "product",
                "warranty",
                "sales_person",
                "sales_contact_number",
                "technical_support",
                "technical_contact_number",
                "supplier_brands_remark"
            ])
            ->setOutput([
                $this->_model->buttons(),
                "brand_name",
                "product",
                "warranty",
                "sales_person",
                "sales_contact_number",
                "technical_support",
                "technical_contact_number",
                "supplier_brands_remark"
            ]);

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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Supplier Brands has been saved successfully!'
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
                $data['message']    = 'Supplier Brands has been updated successfully!';
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
            'message'   => 'Supplier Brands has been retrieved!'
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
            'message'   => 'Supplier Brands has been deleted successfully!'
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
