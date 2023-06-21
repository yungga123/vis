<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InventoryLogsModel;
use App\Models\InventoryModel;
use App\Models\InventoryDropdownModel;
use monken\TablesIgniter;

class InventoryLogs extends BaseController
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
     * Use to initialize inventory dropdowns model
     * @var object
     */
    private $_mdropdown;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_model       = new InventoryLogsModel(); // Current model
        $this->_module_code = MODULE_CODES['inventory']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
        // $this->_mdropdown   = new InventoryDropdownModel();
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

        $data['title']          = 'Inventory Logs';
        $data['page_title']     = '<a href="'. url_to('inventory.home') .'" title="Click here to go to Inventory.">Inventory</a> Logs';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = ['inventory/logs.js'];
        $data['routes']         = json_encode([
            'dropdown' => [
                'show'      => url_to('inventory.dropdown.show'),
            ],
            'logs' => [
                'list'      => url_to('inventory.logs.list'),
                'save'      => url_to('inventory.logs.save'),
            ],
        ]);

        $dropdownModel = new InventoryDropdownModel();
        $data['categories']     = inventory_categories_options($dropdownModel);

        return view('inventory/logs/index', $data);
    }

    /**
     * Saving process of item ins (inserting and updating items)
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => '<strong>ITEM IN!</strong> Item has been saved successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $request    = $this->request->getVar();
            $inputs     = [
                'inventory_id'      => $request['inventory_id'],
                'item_size'         => $request['item_size_logs_out'] ?? $request['item_size_logs'] ?? 0,
                'item_sdp'          => $request['item_sdp_logs'],
                'item_srp'          => $request['item_srp_logs'],
                'project_price'     => $request['project_price_logs'],
                'stocks'            => $request['quantity'] ?? $request['stocks_logs'],
                'parent_stocks'     => $request['parent_stocks'],
                'stock_unit'        => $request['stock_unit_logs_out'] ?? $request['stock_unit_logs'] ?? 0,
                'date_of_purchase'  => $request['date_of_purchase_logs'],
                'location'          => $request['location_logs'],
                'supplier'          => $request['supplier_logs'],
                'status'            => $request['status_logs'] ?? NULL,
                'status_date'       => $request['status_date_logs'] ?? NULL,
                'action'            => $request['action_logs'],
            ];

            log_message('error', 'data logs => '. json_encode($inputs));
            if (! $this->_model->save($inputs)) {
                $errors = $this->_model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";

                foreach ($errors as $key => $value) {
                    $errors[$key .'_logs'] = $value;
                }                
                $data['errors']     = $errors;
            }

            if ($request['action_logs'] === 'ITEM_OUT') {
                $data['message']    = '<strong>ITEM OUT!</strong> Item has been updated successfully!';
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
     * Get list of items
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table = new TablesIgniter();
        $request = $this->request->getVar();

        $table->setTable($this->_model->noticeTable($request))
            ->setSearch([
                'inventory_id',
                'category',
                'sub_category',
                'item_brand',
                'item_model',
                'item_description',
                'encoder',
            ])
            ->setOrder([
                // null,
                'action',
                'inventory_id',
                'category',
                'sub_category',
                'item_brand',
                'item_model',
                'item_description',
                'item_size',
                'stocks',
                'stock_unit',
                'status',
                'status_date',
                'encoder',
            ])
            ->setOutput([
                // $this->_model->buttons($this->_permissions),
                $this->_model->actionLogs(),
                'inventory_id',
                'category',
                'sub_category',
                'item_brand',
                'item_model',
                'item_description',
                'item_size',
                'stocks',
                'stock_unit',
                'status',
                'status_date',
                'encoder',
            ]);

        return $table->getDatatable();
    }
}
