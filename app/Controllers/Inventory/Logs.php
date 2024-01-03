<?php

namespace App\Controllers\Inventory;

use App\Controllers\BaseController;
use App\Models\InventoryLogsModel;
use App\Models\InventoryDropdownModel;
use monken\TablesIgniter;

class Logs extends BaseController
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
     * @var array
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
        $this->_can_add     = $this->checkPermissions($this->_permissions, ACTION_ADD);
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
        $this->checkRolePermissions($this->_module_code, ACTION_VIEW);

        $data['title']          = 'Inventory Logs';
        $data['page_title']     = '<a href="'. url_to('inventory.home') .'" title="Click here to go to Inventory.">Inventory</a> Logs';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = ['inventory/logs/index.js'];
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
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.saved', '<strong>ITEM IN!</strong> Item')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $request    = $this->request->getVar();
                $inputs     = [
                    'inventory_id'      => $request['inventory_id'],
                    'stocks'            => $request['quantity'] ?? $request['stocks_logs'],
                    'parent_stocks'     => $request['parent_stocks'],
                    'status'            => $request['status_logs'] ?? '',
                    'status_date'       => $request['status_date_logs'] ?? NULL,
                    'action'            => $request['action_logs'],
                ];

                if (! $this->_model->save($inputs)) {
                    $errors = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');

                    foreach ($errors as $key => $value) {
                        $errors[$key .'_logs'] = $value;
                    }                
                    $data['errors']     = $errors;
                }

                if ($request['action_logs'] === 'ITEM_OUT') {
                    $data['message']    = res_lang('success.updated', '<strong>ITEM OUT!</strong> Item');
                }
                return $data;
            }
        );

        return $response;
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
                'category_name',
                'subcategory_name',
                'brand',
                'item_model',
                'item_description',
                'encoder',
            ])
            ->setOrder([
                'action',
                'inventory_id',
                'category_name',
                'subcategory_name',
                'brand',
                'item_model',
                'item_description',
                'stocks',
                'parent_stocks',
                'current_stocks',
                'size',
                'unit',
                'cap_status',
                'status_date_formatted',
                'encoder',
                'created_at_formatted',
            ])
            ->setOutput([
                $this->_model->actionLogs(),
                'inventory_id',
                'category_name',
                'subcategory_name',
                'brand',
                'item_model',
                'item_description',
                'stocks',
                'parent_stocks',
                'current_stocks',
                'size',
                'unit',
                'cap_status',
                'status_date_formatted',
                'encoder',
                'created_at_formatted',
            ]);

        return $table->getDatatable();
    }
}
