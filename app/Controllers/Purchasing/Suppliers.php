<?php

namespace App\Controllers\Purchasing;

use App\Controllers\BaseController;
use App\Models\SuppliersModel;
use monken\TablesIgniter;

class Suppliers extends BaseController
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
        $this->_model       = new SuppliersModel(); // Current model
        $this->_module_code = MODULE_CODES['suppliers']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, ACTION_ADD);
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

        $data['title']          = 'Suppliers';
        $data['page_title']     = 'Suppliers | List';
        $data['btn_add_lbl']    = 'Add New Supplier';
        $data['can_add']        = $this->_can_add;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['select2']        = true;
        $data['custom_js']      = [
            'purchasing/suppliers/index.js', 
            'purchasing/suppliers_brand/index.js',
            'dt_filter.js'
        ];
        $data['routes']         = json_encode([
            'supplier' => [
                'list'      => url_to('suppliers.list'),
                'edit'      => url_to('suppliers.edit'),
                'delete'    => url_to('suppliers.delete'),
                'brand'     => [
                    'list'      => url_to('suppliers.brand.list'),
                    'edit'      => url_to('suppliers.brand.edit'),
                    'delete'    => url_to('suppliers.brand.delete'),
                ],
            ],
        ]);

        return view('purchasing/suppliers/index', $data);
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
        $fields     = [
            'id',
            'supplier_name',
            'supplier_type',
            'address',
            'contact_person',
            'contact_number',
            'viber',
            'payment_terms',
            'payment_mode',
            'product',
            'email_address',
            'bank_name',
            'bank_account_name',
            'bank_number',
            'remarks',
            'created_by',
            'created_at',
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.supplier_name",
                "{$this->_model->table}.supplier_type",
                "{$this->_model->table}.address",
                "{$this->_model->table}.contact_person",
                "{$this->_model->table}.contact_number",
                "{$this->_model->table}.viber",
                "{$this->_model->table}.product",
                "{$this->_model->table}.email_address",
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
     * Saving process of suppliers (inserting and updating suppliers)
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.saved', 'Supplier')
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
                $data['message']    = res_lang('success.updated', 'Supplier');
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
            'message'   => res_lang('success.retrieved', 'Supplier')
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
            'message'   => res_lang('success.deleted', 'Supplier')
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = $this->_model;

            $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);
            
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
