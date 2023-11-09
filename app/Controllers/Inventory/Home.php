<?php

namespace App\Controllers\Inventory;

use App\Controllers\BaseController;
use App\Models\InventoryModel;
use App\Models\InventoryDropdownModel;
use App\Traits\ExportTrait;
use monken\TablesIgniter;

/**
 * Controller for Inventory
 */
class Home extends BaseController
{
    /* Declare trait here to use */
    use ExportTrait;

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
        $this->_model       = new InventoryModel(); // Current model
        $this->_module_code = MODULE_CODES['inventory']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
        $this->_mdropdown   = new InventoryDropdownModel();
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

        $dropdowns_link         = '<small><a href="'. url_to('inventory.dropdown.home') .'" title="Click here to go to Inventory Dropdowns">Inventory Dropdowns</a></small>';
        $data['title']          = 'Inventory | Masterlist';
        $data['page_title']     = 'Inventory | Masterlist - '. $dropdowns_link;
        $data['can_add']        = $this->_can_add;
        $data['btn_add_lbl']    = 'Add New Item';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = ['inventory/index.js', 'inventory/logs/index.js'];
        $data['routes']         = json_encode([
            'inventory' => [
                'list'      => url_to('inventory.list'),
                'save'      => url_to('inventory.save'),
                'edit'      => url_to('inventory.edit'),
                'delete'    => url_to('inventory.delete'),
            ],
            'dropdown' => [
                'show'      => url_to('inventory.dropdown.show'),
                'save'      => url_to('inventory.dropdown.save'),
            ],
            'logs' => [
                'save'      => url_to('inventory.logs.save'),
            ],
            'purchasing' => [
                'common' => [
                    'suppliers' => url_to('purchasing.common.suppliers'),
                ]
            ],
        ]);
        $data['categories']     = inventory_categories_options($this->_mdropdown, true);

        return view('inventory/index', $data);
    }

    /**
     * Get list of items
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
            'category_name',
            'subcategory_name',
            'brand',
            'item_model',
            'item_description',
            'size',
            'unit',
            'stocks',
            'item_sdp',
            'total_price',
            'item_srp',
            'project_price',
            'date_purchase',
            'location',
            'created_by_name',
            'created_at_formatted'
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->view}.category_name",
                "{$this->_model->view}.subcategory_name",
                "{$this->_model->view}.brand",
                "{$this->_model->table}.item_model",
                "{$this->_model->table}.item_description",
                "{$this->_model->view}.supplier_name",
            ])
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
     * Saving process of items (inserting and updating items)
     *
     * @return json
     */
    public function save() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Item has been saved successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $inputs = [
                    'id'                => $this->request->getVar('id'),
                    'category'          => $this->request->getVar('category'),
                    'sub_category'      => $this->request->getVar('sub_category') ?? '',
                    'item_brand'        => $this->request->getVar('item_brand') ?? '',
                    'item_model'        => $this->request->getVar('item_model'),
                    'item_description'  => $this->request->getVar('item_description'),
                    'item_size'         => $this->request->getVar('item_size') ?? '',
                    'item_sdp'          => $this->request->getVar('item_sdp'),
                    'item_srp'          => $this->request->getVar('item_srp'),
                    'project_price'     => $this->request->getVar('project_price'),
                    'total'             => $this->request->getVar('total'),
                    'stocks'            => $this->request->getVar('stocks'),
                    'stock_unit'        => $this->request->getVar('stock_unit') ?? '',
                    'date_of_purchase'  => $this->request->getVar('date_of_purchase'),
                    'supplier_id'       => $this->request->getVar('supplier_id'),
                    'location'          => $this->request->getVar('location'),
                ];
    
                if (! $this->_model->save($inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                }
    
                if ($this->request->getVar('id')) {
                    $data['message']    = 'Item has been updated successfully!';
                }
                return $data;
            }
        );

        return $response;
    }
    
    /**
     * For getting the item data using the id
     *
     * @return json
     */
    public function edit() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Item has been retrieved!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $data['data']   = $this->_model->getInventories($id, true, true);
                return $data;
            },
            false
        );

        return $response;
    }

    /**
     * Saving process of items
     *
     * @return json
     */
    public function delete() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Item has been deleted successfully!'
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
        $columns    = "
            {$this->_model->table}.id,
            {$this->_model->view}.supplier_name,
            {$this->_model->view}.category_name,
            {$this->_model->view}.subcategory_name,
            {$this->_model->view}.brand,
            {$this->_model->table}.item_model,
            {$this->_model->table}.item_description,
            {$this->_model->view}.size,
            {$this->_model->view}.unit,
            {$this->_model->table}.stocks,
            ".dt_sql_number_format("{$this->_model->table}.item_sdp")." AS item_sdp,
            ".dt_sql_number_format("({$this->_model->table}.stocks * {$this->_model->table}.item_sdp)")." AS total_price,
            ".dt_sql_number_format("{$this->_model->table}.item_srp")." AS item_srp,
            ".dt_sql_number_format("{$this->_model->table}.project_price")." AS project_price,
            ".dt_sql_date_format("{$this->_model->table}.date_of_purchase")." AS date_of_purchase,
            {$this->_model->table}.location,
            {$this->_model->view}.created_by_name,
            ".dt_sql_datetime_format("{$this->_model->table}.created_at")." AS created_at
        ";
        $builder    = $this->_model->select($columns);

        $this->_model->joinView($builder);
        $builder->where("{$this->_model->table}.deleted_at", null);
        $builder->orderBy("{$this->_model->table}.id", 'ASC');

        $data       = $builder->findAll();
        $header     = [
            'Item #',
            'Supplier',
            'Category',
            'Sub-Category',
            'Item Brand',
            'Item Model',
            'Item Description',
            'Item Size',
            'Item Unit',
            'Quantity',
            "Dealer's Price",
            'Total Price',
            'Retail Price',
            'Project Price',
            'Date of Purchase',
            'Location',
            'Encoder',
            'Encoded At'
        ];
        $filename   = 'Inventory Items Masterlist';

        $this->exportToCsv($data, $header, $filename);
    }
}
