<?php

namespace App\Controllers\Inventory;

use App\Controllers\BaseController;
use App\Models\CustomerBranchModel;
use App\Models\CustomerModel;
use App\Models\InventoryModel;
use App\Models\OrderFormItemModel;
use App\Models\OrderFormModel;
use App\Traits\InventoryTrait;
use App\Traits\GeneralInfoTrait;
use App\Traits\CommonTrait;
use App\Traits\HRTrait;
use monken\TablesIgniter;

class OrderForm extends BaseController
{
    /* Declare trait here to use */
    use InventoryTrait, GeneralInfoTrait, CommonTrait, HRTrait;

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
     * Class constructor
     */
    public function __construct()
    {
        $this->_model       = new OrderFormModel(); // Current model
        $this->_module_code = MODULE_CODES['order_forms']; // Current module
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
        $this->checkRolePermissions($this->_module_code, ACTION_VIEW);

        $data['title']          = 'Inventory | Order Forms';
        $data['page_title']     = 'Inventory | Order Forms';
        $data['can_add']        = $this->_can_add;
        $data['btn_add_lbl']    = 'Add Order Form';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = ['inventory/order_form/index.js', 'dt_filter.js', 'admin/common.js'];
        $data['routes']         = json_encode([
            'order_form' => [
                'list'      => url_to('inventory.order_form.list'),
                'save'      => url_to('inventory.order_form.save'),
                'fetch'     => url_to('inventory.order_form.fetch'),
                'delete'    => url_to('inventory.order_form.delete'),
                'change'    => url_to('inventory.order_form.change'),
            ],
            'admin' => [
                'common' => [
                    'customers'         => url_to('admin.common.customers'),
                    'customer_branches' => url_to('admin.common.customer.branches'),
                ]
            ],
            'inventory' => [
                'common' => [
                    'masterlist'    => url_to('inventory.common.masterlist'),
                ]
            ],
        ]);

        $status = get_prf_status();
        
        unset($status['pending']); // Remove pending

        $data['php_to_js_options'] = json_encode([
            'vat_percent'   => $this->getVatPercent(),
            'status'        => [
                'set' => set_prf_status(),
                'get' => array_values($status),
            ],
        ]);
        
        return view('inventory/order_form/index', $data);
    }

    /**
     * Get list of records
     *
     * @return array|dataTable
     */
    public function list()
    {
        $customerModel  = new CustomerModel();
        $branchModel    = new CustomerBranchModel();
        $table          = new TablesIgniter();
        $request        = $this->request->getVar();
        $builder        = $this->_model->noticeTable($request);
        $fields         = [
            'id',
            'client_name',
            'client_branch_name',
            'purchase_at',
            'total_amount',
            'total_discount',
            'with_vat',
            'vat_amount',
            'grand_total',
            'remarks',
            'created_by',
            'created_at',
            'accepted_by',
            'accepted_at',
            'item_out_by',
            'item_out_at',
            'filed_by',
            'filed_at',
            'rejected_by',
            'rejected_at',
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.id",
                "{$customerModel->table}.name",
                "{$branchModel->view}.branch_name",
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
                        $this->_model->dtViewOrderFormItems(),
                        $this->_model->dtOrderFormStatusFormat(),
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.saved', 'Order Form')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $action         = ACTION_ADD;
                $request        = $this->request->getVar();
                $id             = $request['id'] ?? '';
                $item_ids       = $request['inventory_id'] ?? '';

                if (! empty(get_array_duplicate($item_ids))) {
                    throw new \Exception("There are <strong>duplicate items</strong> in the list! Please double check and remove the duplicate one.", 2);
                }

                // Check restriction
                $this->checkRecordRestrictionViaStatus($id, $this->_model);

                $purchase_at    = $this->_formatPurchaseAt($request['purchase_date'], $request['purchase_time']);
                $is_commercial  = ($request['customer_type'] ?? '') == 'commercial';
                $with_vat       = ($request['with_vat'] ?? '') == '1';
                $vat_amount     = ($request['vat_amount'] ?? 0);
                $grand_total    = ($request['grand_total'] ?? 0);
                $inputs         = [
                    'id'                    => $id,
                    'customer_id'           => $request['customer_id'] ?? '',
                    'customer_branch_id'    => $is_commercial ? ($request['customer_branch_id'] ?? null) : null,
                    'purchase_at'           => $purchase_at,
                    'total_amount'          => $request['total_amount'] ?? '',
                    'total_discount'        => $request['total_discount'] ?? '',
                    'with_vat'              => $with_vat,
                    'vat_amount'            => $with_vat ? $vat_amount : null,
                    'grand_total'           => $with_vat ? $grand_total : ($grand_total - $vat_amount),
                    'remarks'               => $request['remarks'] ?? '',
                ];

                if ($id) {
                    $action             = ACTION_EDIT;
                    $data['message']    = res_lang('success.updated', 'Order Form');
                }

                $this->checkRoleActionPermissions($this->_module_code, $action, true);

                if (! $this->_model->save($inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    $itemModel          = new OrderFormItemModel();
                    $order_form_id      = $id ? $id : $this->_model->insertID();
                    
                    $itemModel->saveItems($request, $order_form_id);
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved', 'Order Form')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id  = $this->request->getVar('id');

                if (! $this->_model->exists($id)) {
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "<strong>Order Form #: {$id}</strong> doesn't exists anymore!";

                    return $data;
                }

                $itemModel = new OrderFormItemModel();
                $items     = $itemModel->getItems($id, true);

                if ($this->request->getVar('items')) {                
                    $data['data']       = $items;
                    $data['message']    = res_lang('success.retrieved', 'Order Form Items');
                } else {
                    $record         = $this->_model->fetch($id, true);
                    $purchase_at    = $this->_formatPurchaseAt($record['purchase_at']);

                    $record['purchase_date']    = $purchase_at[0];
                    $record['purchase_time']    = $purchase_at[1] . ':00';

                    $data['data']               = $record;
                    $data['data']['items']      = $items;
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.deleted', 'Order Form')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id = $this->request->getVar('id');

                // Check restriction
                $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);
                $this->checkRecordRestrictionViaStatus($id, $this->_model);

                if (! $this->_model->delete($id)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                }
                return $data;
            }
        );

        return $response;
    }

    /**
     * Changing status of prf
     *
     * @return json
     */
    public function change() 
    {
        $data       = [];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id         = $this->request->getVar('id');
                $_status    = $this->request->getVar('status');
                $status     = set_prf_status($_status);
                $inputs     = ['status' => $status];
                $item_out   = $status === 'item_out';

                $this->checkRoleActionPermissions($this->_module_code, $_status, true);

                if ($item_out) {
                    if ($this->_checkItemsOutNStocks($id)) {
                        $message = "There is/are item(s)'s <strong>available stocks</strong> are less than the <strong>quantity</strong>!";

                        throw new \Exception($message, 2);
                    }
                }

                if (! $this->_model->update($id, $inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    $data['status']     = res_lang('status.success');
                    $data['message']    = res_lang('success.changed', ['Order Form', strtoupper($status)]);
                    
                    if ($item_out) {
                        // Update inventory stocks
                        $this->_model->updateInventoryStock($id);
                    }
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
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code, ACTION_PRINT);
        
        $itemModel      = new OrderFormItemModel();
        $customerModel  = new CustomerModel();
        $branchModel    = new CustomerBranchModel();
        $columns    = "
            {$this->_model->table}.id,
            {$customerModel->table}.name AS client_name,
            {$customerModel->table}.contact_person AS client_contact_person,
            {$customerModel->table}.contact_number AS client_contact_number,
            ".dt_sql_concat_client_address($customerModel->table, '')." AS client_address,
            {$customerModel->table}.telephone AS client_telephone,
            {$branchModel->table}.branch_name AS client_branch_name,
            {$branchModel->table}.contact_person AS client_branch_contact_person,
            {$branchModel->table}.contact_number AS client_branch_contact_number,
            ".dt_sql_concat_client_address($branchModel->table, '')." AS client_branch_address,
            {$this->_model->table}.purchase_at,
            {$this->_model->table}.with_vat,
            {$this->_model->table}.vat_amount,
            {$this->_model->table}.grand_total,
            {$this->_model->table}.remarks,
            {$this->_model->table}.created_at,
            cb.employee_name AS created_by,
            ab.employee_name AS accepted_by,
            rb.employee_name AS rejected_by,
            ib.employee_name AS item_out_by,
            fb.employee_name AS filed_by,
        ";
        $builder    = $this->_model->select($columns);
        
        $this->_model->joinCustomers($builder, $customerModel, '', true);
        $this->joinAccountView($builder, 'created_by', 'cb');
        $this->joinAccountView($builder, 'accepted_by', 'ab');
        $this->joinAccountView($builder, 'rejected_by', 'rb');
        $this->joinAccountView($builder, 'item_out_by', 'ib');
        $this->joinAccountView($builder, 'filed_by', 'fb');

        $order_form = $builder->first($id);

        // For restriction
        if (empty($order_form)) {
            return $this->redirectTo404Page();
        }
        
        $data['order_form']     = $order_form;
        $data['items']          = $itemModel->getItems($id, true);
        $data['title']          = 'Print Order Form';
        $data['company_logo']   = $this->getCompanyLogo();

        return view('inventory/order_form/print', $data);
    }

    /**
     * Format purchase date and time
     *
     * @param string        Purchase date or date & time
     * @param string|null   Purchase time or null
     * 
     * @return array|string
     */
    private function _formatPurchaseAt($date_or_datetime, $time = null) 
    {
        if (is_null($time)) {
            return [
                format_date($date_or_datetime, 'Y-m-d'), 
                format_time($date_or_datetime, 'H:i')
            ];
        }

        $purchase_at = $date_or_datetime . ' ' . $time;

        return format_datetime($purchase_at, 'Y-m-d H:i:s');
    }

    /**
     * Check if items quantity (out) is greater than the current stocks
     *
     * @param int|array $id    The id(s) to be search
     * @return bool            
     */
    private function _checkItemsOutNStocks($id)
    {
        $itemModel  = new OrderFormItemModel();
        $invModel   = new InventoryModel();
        $columns    = "{$itemModel->table}.quantity, {$invModel->table}.stocks";
        $items      = $itemModel->getItems($id, true, $columns);

        if (! empty($items)) {
            foreach ($items as $val) {
                if (floatval($val['stocks']) < floatval($val['quantity']))
                    return true;
            }
        }

        return false;
    }
}
