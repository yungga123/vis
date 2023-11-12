<?php

namespace App\Controllers\Purchasing;

use App\Controllers\BaseController;
use App\Models\PurchaseOrderModel;
use App\Models\POItemModel;
use App\Models\RequestPurchaseFormModel;
use App\Models\RPFItemModel;
use App\Models\InventoryModel;
use App\Models\SuppliersModel;
use App\Traits\GeneralInfoTrait;
use App\Traits\CommonTrait;
use App\Traits\HRTrait;
use App\Traits\ExportTrait;
use monken\TablesIgniter;

class PurchaseOrder extends BaseController
{
    /* Declare trait here to use */
    use GeneralInfoTrait, CommonTrait, HRTrait, ExportTrait;

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
        $this->_model       = new PurchaseOrderModel(); // Current model
        $this->_module_code = MODULE_CODES['purchase_order']; // Current module
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

        $module_name            = get_modules($this->_module_code);
        $data['title']          = $module_name;
        $data['page_title']     = $module_name;
        $data['can_add']        = $this->_can_add;
        $data['btn_add_lbl']    = 'Add Purchase Order';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = ['purchasing/purchase_order/index.js', 'dt_filter.js'];
        $data['routes']         = json_encode([
            'purchase_order' => [
                'list'      => url_to('purchase_order.list'),
                'fetch'     => url_to('purchase_order.fetch'),
                'change'    => url_to('purchase_order.change'),
                'delete'    => url_to('purchase_order.delete'),
            ],
            'purchasing'    => [
                'common'    => [
                    'rpf'   => url_to('purchasing.common.rpf'),
                ]
            ],
            'rpf'           => [
                'fetch'     => url_to('rpf.fetch'),
            ],
        ]);

        return view('purchasing/purchase_order/index', $data);
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
        $supplierModel  = new SuppliersModel();
        $fields     = [
            'id',
            'supplier_name',
            'attention_to',
            'requested_by',
            'requested_at_formatted',
            'created_by_name',
            'created_at_formatted',
            'approved_by_name',
            'approved_at_formatted',
            'filed_by_name',
            'filed_at_formatted',
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.id",
                "{$supplierModel->table}.supplier_name",
            ])
            ->setOrder(
                array_merge(
                    [null, null, null, null], 
                    $fields
                )
            )
            ->setOutput(
                array_merge(
                    [
                        dt_empty_col(),
                        $this->_model->buttons($this->_permissions),
                        $this->_model->dtViewPOItems(),
                        $this->_model->dtPOStatusFormat(),
                    ], 
                    $fields
                )
            );

        return $table->getDatatable();
    }

    /**
     * Saving process of record (inserting and updating)
     *
     * @return json
     */
    public function save() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Purchase Order has been saved successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $print_po_id = $this->request->getVar('po_id');

                if (! $this->validate($this->_validationRules($print_po_id))) {
                    $data['status']     = STATUS_ERROR;
                    $data ['errors']    = $this->validator->getErrors();
                    $data ['message']   = 'Validation error!';

                    return $data;
                } 

                if ($print_po_id) {
                    // Update Process
                    $set_arr = [
                        'attention_to'  => $this->request->getVar('attention_to'),
                        'with_vat'      => $this->request->getVar('with_vat') ? true : false,
                    ];
                    $this->_model->update($print_po_id, $set_arr);

                    return $data;
                }

                $id     = $this->request->getVar('id');
                $rpf_id = $this->request->getVar('rpf_id');

                $rpfItemModel   = new RPFItemModel();
                $inventoryModel = new InventoryModel();  
                $columns        = "
                    {$inventoryModel->table}.supplier_id,
                    {$rpfItemModel->table}.inventory_id,
                ";
                $builder        = $rpfItemModel->select($columns)->joinInventoryOnly();
                $result         = $builder->where('rpf_id', $rpf_id)->findAll();

                if (! empty($result)) {
                    $rows       = [];
                    $sup_ids    = [];
                    $po_ids     = [];
                    foreach ($result as $val) {
                        $arr = [
                            'rpf_id'        => $rpf_id,
                            'supplier_id'   => $val['supplier_id'],
                            'inventory_id'  => $val['inventory_id'],
                        ];

                        if (! in_array($val['supplier_id'], $sup_ids)) {
                            $po_id          = $this->_model->insert($arr);
                            $po_ids[]       = $po_id;
                            $arr['po_id']   = $po_id;
                        }
                        $sup_ids[] = $val['supplier_id'];
                        $rows[$val['supplier_id']][] = $arr;
                    }

                    // Format $rows value for insertBatch in purchase_order_items table
                    $inputs = [];
                    foreach ($rows as $key => $val) {
                        if (count($val) > 1) {
                            $po_id = null;
                            for ($i=0; $i < count($val); $i++) {
                                // Store first array's po_id in a variable
                                if ($i === 0) $po_id = $val[$i]['po_id'];
                                // Check if has po_id key and if not, add one
                                if (! isset($val[$i]['po_id'])) $val[$i]['po_id'] = $po_id;
                                // Then store the formatted value $inputs
                                $inputs[] = $val[$i];
                            }
                        } else $inputs[] = $val[0];
                    }

                    if (! empty($inputs)) {
                        // Insert the values
                        $poItemModel = new POItemModel();
                        $poItemModel->deletePOItems($po_ids, $rpf_id);
                        $poItemModel->insertBatch($inputs);
                    }
                }

                return $data;
            }
        );

        return $response;
    }
    
    /**
     * For fetching record using the id or other
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Purchase Order has been retrieved!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id         = $this->request->getVar('id');
                $results    = $this->_model->getPOItems($id);

                if ($this->request->getVar('po_items')) {
                    $data['data']       = $results;
                    $data['message']    = 'Purchase Order items has been retrieved!';
                } else {
                    $record                 = $this->_model->getPurchaseOrders($id, 'rpf_id, attention_to');
                    $rpfModel               = new RequestPurchaseFormModel();
                    $rpfColumns             = "
                        DATE_FORMAT(date_needed, '".dt_sql_date_format()."') AS date_needed,
                        DATE_FORMAT(created_at, '".dt_sql_datetime_format()."') AS requested_at,
                        {$rpfModel->view}.created_by_name  AS requested_by
                    ";     
                    $data['data']           = $record;
                    $data['data']['items']  = $results;
                    $data['data']['rpf']    = $rpfModel->joinView($rpfModel)->getRequestPurchaseForms($id, false, $rpfColumns);
                }

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
            'message'   => 'PRF has been deleted successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id = $this->request->getVar('id');

                // Check restriction
                $this->checkRecordRestrictionViaStatus($id, $this->_model);

                if (! $this->_model->delete($id)) {
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
     * Changing status of the record
     *
     * @return json
     */
    public function change() 
    {
        $data       = [];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id     = $this->request->getVar('id');
                $status = set_po_status($this->request->getVar('status'));
                $inputs = ['status' => $status];

                if (! $this->_model->update($id, $inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                } else {
                    $data['status']     = STATUS_SUCCESS;
                    $data['message']    = 'Purchase Order has been '. strtoupper($status) .' successfully!';
                }
                return $data;
            }
        );

        return $response;
    }

    /**
     * Printing record
     *
     * @return view
     */
    public function print($id) 
    {
        // Check role & action if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code, 'PRINT');
        
        $rpfModel               = new RequestPurchaseFormModel();
        $supplierModel          = new SuppliersModel();
        $inventoryModel         = new InventoryModel();
        $columns                = "
            {$this->_model->table}.id,
            {$this->_model->table}.rpf_id, 
            {$this->_model->table}.supplier_id, 
            {$this->_model->table}.attention_to, 
            {$this->_model->table}.with_vat,
            cb.employee_name AS prepared_by_name,
            ab.employee_name AS approved_by_name
        ";
        $builder                = $this->_model->select($columns);
        $this->joinAccountView($builder, "{$this->_model->table}.created_by", 'cb');
        $this->joinAccountView($builder, "{$this->_model->table}.approved_by", 'ab');

        $purchase_order         = $builder->where("{$this->_model->table}.id", $id)->find()[0];
        $items                  = $this->_model->getPOItems($id, "{$inventoryModel->view}.size");
        $supplier               = $supplierModel->getSuppliers(
            $purchase_order['supplier_id'], 
            'supplier_name, address, payment_terms, payment_mode, others_payment_mode'
        );
        $rpfColumns             = "
            DATE_FORMAT(date_needed, '".dt_sql_date_format()."') AS date_needed,
            DATE_FORMAT(created_at, '".dt_sql_datetime_format()."') AS requested_at,
            {$rpfModel->view}.created_by_name  AS requested_by
        ";
        $general_info           = $this->getGeneralInfo(['company_logo', 'company_name', 'company_address'], true);
        $data['purchase_order'] = $purchase_order;
        $data['supplier']       = $supplier;
        $data['items']          = $items;
        $data['rpf']            = $rpfModel->getRequestPurchaseForms($purchase_order['rpf_id'], true, $rpfColumns);
        $data['general_info']   = $general_info;
        $data['title']          = 'Generate Purchase Order';
        $data['disable_auto_print'] = true;
        $data['custom_js']      = ['functions.js', 'purchasing/purchase_order/print.js'];

        return view('purchasing/purchase_order/print', $data);
    }

    /**
     * For exporting data to csv
     *
     * @return void
     */
    public function export() 
    {
        $supplierModel  = new SuppliersModel();
        $rpfModel       = new RequestPurchaseFormModel();
        $columns        = "
            UPPER({$this->_model->table}.status) AS status,
            {$this->_model->table}.id,
            {$supplierModel->table}.supplier_name,
            {$this->_model->table}.attention_to,
            IF({$this->_model->table}.with_vat = 0, 'NO', 'YES') AS with_vat,
            {$rpfModel->view}.created_by_name AS requested_by,
            ".dt_sql_datetime_format("{$rpfModel->table}.created_at")." AS requested_at,
            cb.employee_name AS generated_by,
            ".dt_sql_datetime_format("{$this->_model->table}.created_at")." AS generated_at,
            ab.employee_name AS approved_by,
            ".dt_sql_datetime_format("{$this->_model->table}.approved_at")." AS approved_at,
            fb.employee_name AS filed_by,
            ".dt_sql_datetime_format("{$this->_model->table}.filed_at")." AS filed_at
        ";
        $builder    = $this->_model->select($columns);

        $this->_model->joinSupplier($builder, $supplierModel);
        $this->_model->joinRpf($builder, $rpfModel);
        $this->_model->joinRpf($builder, $rpfModel, true);
        $this->joinAccountView($builder, "{$this->_model->table}.created_by", 'cb');
        $this->joinAccountView($builder, "{$this->_model->table}.approved_by", 'ab');
        $this->joinAccountView($builder, "{$this->_model->table}.filed_by", 'fb');

        $builder->where("{$this->_model->table}.deleted_at", null);
        $builder->orderBy("{$this->_model->table}.id", 'ASC');

        $data       = $builder->findAll();
        $header     = [
            'Status',
            'PO #',
            'Supplier',
            'Attention To',
            'With Vat',
            'Requested By',
            'Requested At',
            'Generated By',
            'Generated At',
            'Approved By',
            'Approved At',
            'Filed By',
            'Filed At',
        ];
        $filename   = 'Purchase Orders';

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * For exporting data to csv
     *
     * @return void
     */
    public function exportItems() 
    {
        $poItemModel    = new POItemModel();
        $rpfItemModel   = new RPFItemModel();
        $inventoryModel = new InventoryModel();
        $columns        = "
            {$poItemModel->table}.po_id,
            {$rpfItemModel->table}.rpf_id,
            {$rpfItemModel->table}.inventory_id,
            {$inventoryModel->view}.supplier_name,
            {$inventoryModel->view}.brand,
            {$inventoryModel->table}.item_model,
            {$inventoryModel->table}.item_description,
            {$inventoryModel->table}.stocks,
            {$inventoryModel->view}.size,
            {$inventoryModel->view}.unit,
            {$rpfItemModel->table}.quantity_in,
            ".dt_sql_number_format("{$inventoryModel->table}.item_sdp")." AS item_price,
            ".dt_sql_number_format("{$inventoryModel->table}.item_sdp * {$rpfItemModel->table}.quantity_in")." AS total_price,
            UPPER({$this->_model->table}.status) AS status,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$this->_model->table}.created_at")." AS created_at
        ";
        $builder        = $this->_model->select($columns);

        $this->_model->joinPOItem($builder, $poItemModel);
        $this->_model->joinRpfItem($builder, $rpfItemModel);
        $poItemModel->joinInventory($builder, $inventoryModel);
        $poItemModel->joinInventory($builder, $inventoryModel, true);
        $this->joinAccountView($builder, "{$this->_model->table}.created_by", 'cb');

        $builder->where("{$this->_model->table}.deleted_at", null);
        $builder->orderBy("{$poItemModel->table}.po_id", 'ASC');

        $data       = $builder->findAll();
        $header     = [
            'PO #',
            'RPF #',
            'Item #',
            'Supplier',
            'Item Brand',
            'Item Model',
            'Item Description',
            'Item Size',
            'Item Unit',
            'Current Stocks',
            'Quantity In',
            'Item Price',
            'Total Price',
            'Status',
            'Created By',
            'Created At',
        ];
        $filename   = 'RPF Items';

        $this->exportToCsv($data, $header, $filename);
    }
    
    /**
     * Validation rules
     * 
     * @return array
     */
    private function _validationRules($print = false)
    {
        if ($print) {
            return [
                'attention_to' => [
                    'rules' => 'required|min_length[5]|max_length[255]',
                    'label' => 'attention to'
                ],
            ];
        }

        return [
            'rpf_id' => [
                'rules' => 'required',
                'label' => 'request purchase forms'
            ],
        ];
    }
}
