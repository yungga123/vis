<?php

namespace App\Controllers\Finance;

use App\Controllers\BaseController;
use App\Models\BillingInvoiceModel;
use App\Models\CustomerModel;
use App\Models\FundsHistoryModel;
use App\Models\TaskLeadView;
use App\Traits\CommonTrait;
use App\Traits\GeneralInfoTrait;
use App\Traits\HRTrait;
use monken\TablesIgniter;

class BillingInvoice extends BaseController
{
    /* Declare trait here to use */
    use CommonTrait, HRTrait, GeneralInfoTrait;

    /**
     * Use to initialize model class
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
        $this->_model           = new BillingInvoiceModel(); // Current model
        $this->_module_code     = MODULE_CODES['billing_invoice']; // Current module
        $this->_permissions     = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add         = $this->checkPermissions($this->_permissions, ACTION_ADD);
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

        $data['title']          = 'Finance | Billing Invoices';
        $data['page_title']     = 'Finance | Billing Invoices';
        $data['btn_add_lbl']    = 'Create Billing Invoice';
        $data['can_add']        = $this->_can_add;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['toastr']         = true;
        $data['select2']        = true;
        $data['custom_js']      = ['finance/billing_invoice/index.js', 'dt_filter.js'];
        $data['routes']         = json_encode([
            'billing_invoice' => [
                'list'      => url_to('finance.billing_invoice.list'),
                'fetch'     => url_to('finance.billing_invoice.fetch'),
                'delete'    => url_to('finance.billing_invoice.delete'),
            ],
            'admin' => [
                'common' => [
                    'quotations' => url_to('admin.common.quotations'),
                ]
            ]
        ]);

        return view('finance/billing_invoice/index', $data);
    }

    /**
     * Get list of records
     *
     * @return array|dataTable
     */
    public function list()
    {
        $tlVModel   = new TaskLeadView();
        $table      = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request, $this->_permissions);
        $fields     = [
            'id',
            'tasklead_id',
            'quotation',
            'client',
            'manager',
            'quotation_type',
            'due_date',
            'bill_type',
            'billing_amount',
            'payment_method',
            'amount_paid',
            'paid_at',
            'attention_to',
            'with_vat',
            'created_by',
            'created_at',
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.id",
                "{$tlVModel->table}.quotation",
                "{$tlVModel->table}.client",
                "{$tlVModel->table}.manager",
            ])
            ->setOrder(array_merge([null, null, null], $fields))
            ->setOutput(
                array_merge(
                    [
                        dt_empty_col(), 
                        $this->_model->buttons($this->_permissions),
                        $this->_model->dtStatusFormat(),
                    ], 
                    $fields
                )
            );
        
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.added', 'Billing Invoice')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $request        = $this->request->getVar();
                $inputs         = [
                    'id'                => $id,
                    'tasklead_id'       => $request['tasklead_id'] ?? null,
                    'due_date'          => $request['due_date'] ?? null,
                    'bill_type'         => $request['bill_type'] ?? null,
                    'payment_method'    => $request['payment_method'] ?? null,
                    'billing_status'    => $request['billing_status'] ?? null,
                    'billing_amount'    => $request['billing_amount'] ?? null,
                    'amount_paid'       => $request['amount_paid'] ?? null,
                ];
                $action         = empty($id) ? ACTION_ADD : ACTION_EDIT;

                if (! empty($request['attention_to'] ?? '')) {
                    $inputs     = [
                        'id'            => $id,
                        'attention_to'  => $request['attention_to'] ?? null,
                        'with_vat'      => ($request['with_vat'] ?? 'no') == 'yes',
                    ];
                } else {
                    $this->checkRoleActionPermissions($this->_module_code, $action, true);
                    $this->checkRecordRestrictionViaStatus($id, $this->_model, 'billing_status');

                    if (($request['billing_status'] ?? '') === 'paid') {
                        $inputs['paid_at']  = current_datetime();
    
                        $this->_model->makeAmountPaidRequired();

                        // Update funds
                        $this->saveCompanyFunds($request['amount_paid']);
                        
                        // Save funds transaction history
                        $fundHModel = new FundsHistoryModel();
                        $fundHModel->save([
                            'billing_invoiced_id'   => $id,
                            'current_funds'         => $this->getCompanyFunds(),
                            'transaction_amount'    => $request['amount_paid'],
                            'coming_from'           => 'Billing Invoice',
                        ]); 
                    }
                }
    
                if ($id) {
                    $data['message']    = res_lang('success.updated', 'Billing Invoice');
                }

                if (! $this->_model->save($inputs)) {
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
     * For getting the item data using the id
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved', 'Billing Invoice')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id         = $this->request->getVar('id');
                $record     = $this->_model->fetch($id, true);

                $data['data'] = $record;
                return $data;
            },
            false
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
        $data = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.deleted', 'Billing Invoice')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id = $this->request->getVar('id');

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
     * Printing record
     *
     * @return view
     */
    public function print($id) 
    {
        // Check role & action if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code, ACTION_PRINT);
        
        $tlVModel   = new TaskLeadView();
        $custModel  = new CustomerModel();
        $columns    = "
            {$this->_model->table}.id,
            {$this->_model->table}.tasklead_id,
            {$this->_model->table}.due_date,
            {$this->_model->table}.bill_type,
            {$this->_model->table}.billing_amount,
            {$this->_model->table}.billing_status,
            {$this->_model->table}.payment_method,
            {$this->_model->table}.amount_paid,
            {$this->_model->table}.paid_at,
            {$this->_model->table}.attention_to,
            {$this->_model->table}.with_vat,
            {$this->_model->table}.created_at,
            {$tlVModel->table}.quotation_num AS quotation,
            {$tlVModel->table}.customer_id AS client_id,
            {$tlVModel->table}.customer_name AS client,
            {$tlVModel->table}.employee_name AS manager,
            {$tlVModel->table}.tasklead_type AS quotation_type,
            {$tlVModel->table}.project,
            {$tlVModel->table}.project_amount,
            cb.employee_name AS created_by,
            cb.position AS created_by_position,
            ".dt_sql_concat_client_address('', 'client_address')."
        ";
        $builder                = $this->_model->select($columns);

        $this->_model->joinBookedTasklead($builder, $tlVModel);
        $this->joinAccountView($builder, "{$this->_model->table}.created_by", 'cb');
        $builder->join($custModel->table, "{$tlVModel->table}.customer_id = {$custModel->table}.id", 'left');

        $billing_invoice         = $builder->where("{$this->_model->table}.id", $id)->first();

        // For restriction
        if (empty($billing_invoice)) {
            return $this->redirectTo404Page();
        }

        // Get general info
        $keys = [
            'vat_percent',
            'billing_invoice_form_code',
        ];
        $keys = array_merge($this->getCompanyInfo([], true), $keys);
        $info = $this->getGeneralInfo($keys, true);

        $data['billing_invoice'] = $billing_invoice;
        $data['general_info']   = $info;
        $data['company_info']   = $this->getCompanyInfo($info);
        $data['title']          = 'Print Billing Invoice';
        $data['disable_auto_print'] = true;
        $data['custom_js']      = [
            'initialize.js',
            'functions.js',
            'finance/billing_invoice/print.js'
        ];

        return view('finance/billing_invoice/print', $data);
    }
}
