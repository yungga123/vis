<?php

namespace App\Controllers\Inventory;

use App\Controllers\BaseController;
use App\Models\ProjectRequestFormModel;
use monken\TablesIgniter;

class ProjectRequestForm extends BaseController
{
    /**
     * Use to initialize corresponding model
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
        $this->_model       = new ProjectRequestFormModel(); // Current model
        $this->_module_code = MODULE_CODES['inventory_prf']; // Current module
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

        $data['title']          = 'Project Request Forms';
        $data['page_title']     = 'Project Request Forms';
        $data['can_add']        = $this->_can_add;
        $data['btn_add_lbl']    = 'Add Project Request Form';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = 'inventory/prf/index.js';
        $data['routes']         = json_encode([
            'prf' => [
                'list'      => url_to('prf.list'),
                'save'      => url_to('prf.save'),
                'fetch'     => url_to('prf.fetch'),
                'delete'    => url_to('prf.delete'),
                'change'    => url_to('prf.change'),
            ],
            'inventory' => [
                'common' => [
                    'masterlist' => url_to('inventory.common.masterlist'),
                ]
            ]
        ]);

        return view('inventory/prf/index', $data);
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

        $table->setTable($builder)
            ->setSearch([
                'brand',
                'item_model',
                'item_description',
                'category_name',
                'subcategory_name',
            ])
            ->setOrder([
                null,
                null,
                'id',
                'inventory_id',
                'category_name',
                'subcategory_name',
                'brand',
                'item_model',
                'item_description',
                'stocks',
                'quantity_out',
                'process_date_formatted',
                'created_by_name',
                'created_at_formatted',
                'accepted_by_name',
                'accepted_at_formatted',
                'rejected_by_name',
                'rejected_at_formatted',
                'item_out_by_name',
                'item_out_at_formatted',
            ])
            ->setOutput([
                $this->_model->buttons($this->_permissions),
                $this->_model->dtPRFStatusFormat(),
                'id',
                'inventory_id',
                'category_name',
                'subcategory_name',
                'brand',
                'item_model',
                'item_description',
                'stocks',
                'quantity_out',
                'process_date_formatted',
                'created_by_name',
                'created_at_formatted',
                'accepted_by_name',
                'accepted_at_formatted',
                'rejected_by_name',
                'rejected_at_formatted',
                'item_out_by_name',
                'item_out_at_formatted',
            ]);

        return $table->getDatatable();
    }

    /**
     * Saving process of record (inserting and updating)
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'PRF has been saved successfully!'
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
                $data['message']    = 'PRF has been updated successfully!';
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
     * For fetching record using the id
     *
     * @return json
     */
    public function fetch() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'PRF has been retrieved!'
        ];

        try {
            $table          = $this->_model->table;
            $tableInventory = $this->_model->tableInventory;
            $columns        = "
                {$table}.id, {$table}.inventory_id, {$table}.quantity_out, {$table}.process_date,                
                CONCAT({$tableInventory}.id, ' | ', {$tableInventory}.item_model, ' | ', {$tableInventory}.item_description) AS text
            ";
            $id             = $this->request->getVar('id');
            $data['data']   = $this->_model->getProjectRequestForms($id, true, $columns);
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
            'message'   => 'PRF has been deleted successfully!'
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
     * Changing status of prf
     *
     * @return json
     */
    public function change() 
    {
        $data = [];

        // Using DB Transaction
        $this->transBegin();

        try {
            $id     = $this->request->getVar('id');
            $status = set_prf_status($this->request->getVar('status'));
            $inputs = ['status' => $status];

            if (! $this->_model->update($id, $inputs)) {
                $data['errors']     = $this->_model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            } else {
                $data['status']     = STATUS_SUCCESS;
                $data['message']    = 'PRF has been '. strtoupper($status) .' successfully!';
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
     * Printing record
     *
     * @return view
     */
    public function print() 
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code);
        
        $id             = $this->request->getUri()->getSegment(3);
        $data['prf']    = $this->_model->getProjectRequestForms($id, true, true);
        $data['title']  = 'Print Project Request Form';

        return view('inventory/prf/print', $data);
    }
}
