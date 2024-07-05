<?php

namespace App\Controllers\Finance;

use App\Controllers\BaseController;
use App\Models\BillingInvoiceOrderFormModel;
use App\Models\CustomerBranchModel;
use App\Models\CustomerModel;
use App\Models\OrderFormItemModel;
use App\Models\OrderFormModel;
use App\Models\TaskLeadView;
use App\Traits\CommonTrait;
use App\Traits\FinanceTrait;
use App\Traits\GeneralInfoTrait;
use App\Traits\HRTrait;
use monken\TablesIgniter;

class BillingInvoiceOrderForms extends BaseController
{
    /* Declare trait here to use */
    use CommonTrait, HRTrait, GeneralInfoTrait, FinanceTrait;

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
     * Use to get current module title
     * @var string
     */
    private $_module_title;

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
        $this->_model           = new BillingInvoiceOrderFormModel(); // Current model
        $this->_module_code     = MODULE_CODES['billing_invoice_order_forms']; // Current module
        $this->_module_title    = MODULES[$this->_module_code]; // Current module title
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

        $data['title']          = 'Finance | ' . $this->_module_title;
        $data['page_title']     = 'Finance | ' . $this->_module_title;
        $data['btn_add_lbl']    = 'Create Billing Invoice';
        $data['can_add']        = $this->_can_add;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['toastr']         = true;
        $data['select2']        = true;
        $data['custom_js']      = ['finance/billing_invoice_order_forms/index.js', 'dt_filter.js'];
        $data['routes']         = json_encode([
            'billing_invoice_order_forms' => [
                'list'      => url_to('finance.billing_invoice_order_forms.list'),
                'fetch'     => url_to('finance.billing_invoice_order_forms.fetch'),
                'delete'    => url_to('finance.billing_invoice_order_forms.delete'),
                'change'    => url_to('finance.billing_invoice_order_forms.change'),
            ],
            'inventory' => [
                'common' => [
                    'order_forms'   => url_to('inventory.common.order_forms'),
                ],
                'order_form' => [
                    'fetch'         => url_to('inventory.order_form.fetch'),
                ],
            ]
        ]);
        $data['php_to_js_options'] = json_encode([
            'overdue_interests' => $this->overdueInterests(),
            'vat_percent'       => $this->getVatPercent(),
        ]);

        // Check overdue billing invoice (Order Forms)
        $this->_model->checkOverdues();

        return view('finance/billing_invoice_order_forms/index', $data);
    }

    /**
     * Get list of records
     *
     * @return array|dataTable
     */
    public function list()
    {
        $custModel  = new CustomerModel();
        $cbModel    = new CustomerBranchModel();
        $table      = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request, $this->_permissions);
        $fields     = [
            'id',
            'order_form_id',
            'client',
            'client_branch',
            'due_date',
            'bill_type',
            'payment_method',
            'billing_amount',
            'receipt_number',
            'overdue_interest',
            'withholding_tax',
            'amount_paid',
            'date_paid',
            'paid_at',
            'attention_to',
            'with_vat',
            'vat_amount',
            'additional_description',
            'created_by',
            'created_at',
            'approved_by',
            'approved_at',
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.id",
                "{$custModel->table}.name",
                "{$cbModel->table}.branch_name",
            ])
            ->setOrder(array_merge([null, null, null, null], $fields))
            ->setOutput(
                array_merge(
                    [
                        dt_empty_col(),
                        $this->_model->buttons($this->_permissions),
                        $this->_model->dtStatusFormat(),
                        $this->_model->dtBillingStatusFormat(),
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
            'message'   => res_lang('success.added', $this->_module_title)
        ];
        $response   = $this->customTryCatch(
            $data,
            function ($data) {
                $id             = $this->request->getVar('id');
                $request        = $this->request->getVar();
                $with_vat       = ($request['with_vat'] ?? 0) == 1;
                $inputs         = [
                    'id'                => $id,
                    'order_form_id'     => $request['order_form_id'] ?? null,
                    'due_date'          => $request['due_date'] ?? null,
                    'bill_type'         => $request['bill_type'] ?? null,
                    'payment_method'    => $request['payment_method'] ?? null,
                    'billing_amount'    => $request['billing_amount'] ?? null,
                    'receipt_number'    => $request['receipt_number'] ?? null,
                    'amount_paid'       => $request['amount_paid'] ?? null,
                    'withholding_tax_percent' => $request['withholding_tax_percent'] ?? null,
                    'withholding_tax'   => $request['withholding_tax'] ?? null,
                    'with_vat'          => $with_vat,
                    'vat_amount'        => $with_vat ? ($request['vat_amount'] ?? null) : null,
                    'grand_total'       => $request['grand_total'] ?? null,
                    'overdue_interest'  => $request['overdue_interest'] ?? null,
                    'additional_description'  => $request['additional_description'] ?? null,
                ];
                $is_paid        = ($request['billing_status'] ?? '') === 'paid';
                $action         = empty($id) ? ACTION_ADD : ACTION_EDIT;
                $action         = $is_paid ? 'MARK_PAID' : $action;

                if ($id) {
                    $data['message']    = res_lang('success.updated', $this->_module_title);
                }

                if (!empty($request['attention_to'] ?? '')) {
                    $inputs     = [
                        'id'            => $id,
                        'attention_to'  => $request['attention_to'] ?? null,
                    ];
                } else {
                    $this->checkRoleActionPermissions($this->_module_code, $action, true);
                    $this->checkRecordRestrictionViaStatus($id, $this->_model, 'billing_status');

                    $overdues = $this->checkNCalculateOverdues($request['billing_amount'], $request['due_date']);

                    if (!empty($overdues) && empty($id)) {
                        $inputs['billing_status']   = 'overdue';
                        $inputs['overdue_interest'] = 0;
                    }

                    if (empty($request['with_interest'] ?? null)) {
                        $inputs['overdue_interest'] = 0;
                    }

                    if ($is_paid) {
                        $inputs['billing_status']   = 'paid';
                        $inputs['date_paid']        = $request['date_paid'] ?? null;
                        $inputs['paid_by']          = session('username');
                        $inputs['paid_at']          = current_datetime();

                        $this->_model->makeAmountPaidRequired();

                        // Update funds
                        $this->saveCompanyFunds($request['amount_paid']);

                        log_msg($id);

                        // Save funds transaction history
                        $params = [
                            'billing_invoice_id'    => $id,
                            'current_funds'         => $this->getCompanyFunds(),
                            'transaction_amount'    => $request['amount_paid'],
                            'transaction_type'      => 'incoming',
                            'coming_from'           => $this->_module_title,
                            'module_code'           => $this->_module_code,
                        ];
                        $this->saveFundTransaction($params);

                        $data['message'] = res_lang('success.paid', $this->_module_title);
                    } else {
                        $inputs['amount_paid'] = 0;
                    }
                }

                if (!$this->_model->save($inputs)) {
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
            'message'   => res_lang('success.retrieved', $this->_module_title)
        ];
        $response   = $this->customTryCatch(
            $data,
            function ($data) {
                $id         = $this->request->getVar('id');
                $record     = $this->_model->fetch($id, true);
                $compare_to = $record['billing_status'] === 'paid' ? $record['paid_at'] : null;
                $overdues   = $this->checkNCalculateOverdues($record['billing_amount'], $record['due_date'], $compare_to);

                if (!empty($overdues)) {
                    $record['days_overdue']   = $overdues['days'];
                }

                $ofItems    = new OrderFormItemModel();
                $items      = $ofItems->getItems($id, true);

                $data['data']           = $record;
                $data['data']['items']  = $items;

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
            'message'   => res_lang('success.deleted', $this->_module_title)
        ];
        $response   = $this->customTryCatch(
            $data,
            function ($data) {
                $id = $this->request->getVar('id');

                $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);
                $this->checkRecordRestrictionViaStatus($id, $this->_model, 'billing_status');
                $this->checkRecordRestrictionViaStatus($id, $this->_model);

                if (!$this->_model->delete($id)) {
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
     * Changing status of billing invoice (Order Forms)
     *
     * @return json
     */
    public function change()
    {
        $data       = [];
        $response   = $this->customTryCatch(
            $data,
            function ($data) {
                $id         = $this->request->getVar('id');
                $status     = 'approved';
                $inputs     = [
                    'status'        => $status,
                    'approved_by'   => session('username'),
                    'approved_at'   => current_datetime(),
                ];

                $this->checkRoleActionPermissions($this->_module_code, 'approve', true);

                if (!$this->_model->update($id, $inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    $data['status']     = res_lang('status.success');
                    $data['message']    = res_lang('success.changed', [$this->_module_title, strtoupper($status)]);
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

        $ofModel    = new OrderFormModel();
        $custModel  = new CustomerModel();
        $branchModel = new CustomerBranchModel();

        $columns    = array_merge([$this->_model->primaryKey], $this->_model->allowedFields);
        $columns    = array_map(function ($column) {
            return "{$this->_model->table}.{$column}";
        }, $columns);

        $columns    = implode(',', $columns);
        $columns    .= ",
            {$this->_model->table}.created_at,
            {$this->_model->table}.approved_at,
            {$custModel->table}.name AS client,
            {$branchModel->table}.branch_name AS client_branch,
            cb.employee_name AS created_by,
            ab.employee_name AS approved_by,
            cb.position AS created_by_position,
            ab.position AS approved_by_position,
            {$ofModel->table}.customer_id AS client_id,
            " . dt_sql_concat_client_address('', 'client_address') . "
        ";
        $builder    = $this->_model->select($columns);

        $this->_model->joinOrderForms($builder, null, true);

        $this->joinAccountView($builder, "{$this->_model->table}.created_by", 'cb');
        $this->joinAccountView($builder, "{$this->_model->table}.approved_by", 'ab');

        $billing_invoice = $builder->where("{$this->_model->table}.id", $id)->first();

        // For restriction
        if (empty($billing_invoice)) {
            return $this->redirectTo404Page();
        }

        // Get general info
        $keys = [
            'vat_percent',
            'billing_invoice_order_forms_form_code',
        ];
        $keys = array_merge($this->getCompanyInfo([], true), $keys);
        $info = $this->getGeneralInfo($keys, true);

        $data['billing_invoice'] = $billing_invoice;
        $data['general_info']   = $info;
        $data['company_info']   = $this->getCompanyInfo($info);
        $data['title']          = 'Print Billing Invoice (Order Forms)';
        $data['disable_auto_print'] = true;
        $data['sweetalert2']    = true;
        $data['custom_js']      = [
            'initialize.js',
            'functions.js',
            'finance/billing_invoice/print.js'
        ];

        return view('finance/billing_invoice/print', $data);
    }
}
