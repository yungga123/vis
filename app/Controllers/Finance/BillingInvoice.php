<?php

namespace App\Controllers\Finance;

use App\Controllers\BaseController;
use App\Models\BillingInvoiceModel;
use monken\TablesIgniter;

class BillingInvoice extends BaseController
{
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

        $data['title']          = 'Finace | Billing Invoices';
        $data['page_title']     = 'Finace | Billing Invoices';
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
        $table      = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request, $this->_permissions);
        $fields     = [
            'employee_id',
            'employee_name',
            'leave_type',
            'start_date',
            'end_date',
            'total_days',
            'leave_reason',
            'leave_remark',
            'created_at',
            'processed_by',
            'processed_at',
            'approved_by',
            'approved_at',
            'discarded_by',
            'discarded_at',
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.employee_id",
                // "{$empModel->table}.employee_name",
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
                    'due_date'          => $request['due_date'],
                    'bill_type'         => $request['bill_type'],
                    'payment_method'    => $request['payment_method'],
                    'status'            => $request['status'],
                    'billing_amount'    => $request['billing_amount'],
                    'amount_paid'       => $request['amount_paid'],
                ];
                $action         = empty($id) ? ACTION_ADD : ACTION_EDIT;

                $this->checkRoleActionPermissions($this->_module_code, $action, true);

                if (($request['status'] ?? '') === 'paid') {
                    $inputs['paid_at']  = current_datetime();

                    $this->_model->makeAmountPaidRequired();
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
                $record     = $this->_model->fetch($id);

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
}
