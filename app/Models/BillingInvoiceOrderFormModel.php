<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;
use App\Traits\FilterParamTrait;

class BillingInvoiceOrderFormModel extends BaseModel
{
    /* Declare trait here to use */
    use HRTrait, FilterParamTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'billing_invoices_order_forms';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'status',
        'order_form_id',
        'billing_status',
        'due_date',
        'bill_type',
        'billing_amount',
        'payment_method',
        'amount_paid',
        'withholding_tax_percent',
        'withholding_tax',
        'date_paid',
        'paid_at',
        'paid_by',
        'receipt_number',
        'attention_to',
        'with_vat',
        'vat_amount',
        'overdue_interest',
        'grand_total',
        'additional_description',
        'approved_at',
        'approved_by',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'order_form_id'     => [
            'rules' => 'if_exist|required',
            'label' => 'task lead',
        ],
        'due_date'     => [
            'rules' => 'if_exist|required',
            'label' => 'due date',
        ],
        'bill_type'   => [
            'rules' => 'if_exist|required',
            'label' => 'bill type',
        ],
        'billing_amount'   => [
            'rules' => 'if_exist|required|numeric',
            'label' => 'billing amount',
        ],
        'payment_method'   => [
            'rules' => 'if_exist|required',
            'label' => 'payment method',
        ],
        'amount_paid'   => [
            'rules' => 'permit_empty|numeric',
            'label' => 'amount paid',
        ],
        'date_paid'   => [
            'rules' => 'permit_empty',
            'label' => 'date paid',
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedByValue'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setStatusByAndAt'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Custom variables
    // Restrict edit/delete action for this statuses
    protected $restrictedStatuses = ['paid', 'approved'];

    /**
     * Make amount_paid required
     */
    public function makeAmountPaidRequired()
    {
        $this->validationRules['amount_paid']['rules']  = 'required|numeric';
        $this->validationRules['date_paid']['rules']    = 'required';
    }

    /**
     * Check overdue billing invoices
     */
    public function checkOverdues()
    {
        $self       = clone $this;
        $overdues   = $this->select('id')
            ->where("DATE_FORMAT(due_date, '%Y-%m-%d') <", current_date())
            ->where('billing_status', 'pending')
            ->findAll(100);

        if (!empty($overdues)) {
            $ids    = format_results($overdues, 'id', '', true);
            $data   = ['billing_status' => 'overdue'];

            $self->set($data)->whereIn('id', $ids)->update();
        }
    }

    /**
     * Fetch records
     * 
     * @param string|int|array $id
     */
    public function fetch($id, $joinOrderForms = false): array|null
    {
        $model      = new OrderFormModel();
        $columns    = array_merge([$this->primaryKey], $this->allowedFields);

        if ($joinOrderForms) {
            $customerModel  = new CustomerModel();
            $branchModel    = new CustomerBranchModel();

            $columns = array_map(function ($column) {
                return "{$this->table}.{$column}";
            }, $columns);

            $columns = implode(',', $columns);
            $columns .= ",
                {$customerModel->table}.name AS client_name,
                {$branchModel->table}.branch_name AS client_branch_name,
                " . dt_sql_datetime_format("{$model->table}.purchase_at") . " AS purchase_at,
                {$model->table}.total_amount,
                {$model->table}.total_discount,
                {$model->table}.vat_amount AS of_vat_amount,
                {$model->table}.grand_total AS of_grand_total
            ";
        }

        $builder = $this->select($columns);

        if ($joinOrderForms) {
            $this->joinOrderForms($builder, $model, true);
        }

        $builder->where("{$this->table}.deleted_at IS NULL");

        if (is_array($id)) {
            $builder->whereIn("{$this->table}.id", $id);

            return $this->findAll();
        }

        $builder->where("{$this->table}.id", $id);

        return $builder->first();
    }

    /**
     * Join order_forms
     */
    public function joinOrderForms($builder = null, $model = null, $client = false, $type = 'left')
    {
        $model      ??= new OrderFormModel();
        $builder    ??= $this;

        $builder->join($model->table, "{$this->table}.order_form_id = {$model->table}.id", $type);

        if ($client) {
            $this->joinCustomers($builder, $model->table, true);
        }

        return $this;
    }

    /**
     * DataTable columns
     */
    public function dtColumns(): string
    {
        $customerModel  = new CustomerModel();
        $branchModel    = new CustomerBranchModel();
        $columns        = "
            {$this->table}.id,
            {$this->table}.status,
            {$this->table}.order_form_id,
            {$customerModel->table}.name AS client,
            {$branchModel->table}.branch_name AS client_branch,
            " . dt_sql_date_format("{$this->table}.due_date") . " AS due_date,
            {$this->table}.bill_type,
            " . dt_sql_number_format("{$this->table}.billing_amount") . " AS billing_amount,
            " . dt_sql_number_format("{$this->table}.overdue_interest") . " AS overdue_interest,
            {$this->table}.billing_status,
            {$this->table}.payment_method,
            " . dt_sql_number_format("{$this->table}.amount_paid") . " AS amount_paid,
            " . dt_sql_number_format("{$this->table}.withholding_tax") . " AS withholding_tax,
            " . dt_sql_date_format("{$this->table}.date_paid") . " AS date_paid,
            " . dt_sql_datetime_format("{$this->table}.paid_at") . " AS paid_at,
            {$this->table}.receipt_number,
            {$this->table}.attention_to,
            IF({$this->table}.with_vat = 0, 'NO', 'YES') AS with_vat,
            " . dt_sql_number_format("{$this->table}.vat_amount") . " AS vat_amount,
            {$this->table}.additional_description,
            cb.employee_name AS created_by,
            ab.employee_name AS approved_by,
            " . dt_sql_datetime_format("{$this->table}.created_at") . " AS created_at,
            " . dt_sql_datetime_format("{$this->table}.approved_at") . " AS approved_at
        ";

        return $columns;
    }

    /**
     * For dataTables
     */
    public function noticeTable($request)
    {
        $builder = $this->db->table($this->table);

        $builder->select($this->dtColumns());

        // Join with other tables
        $this->joinOrderForms($builder, null, true);
        $this->joinAccountView($builder, 'created_by', 'cb');
        $this->joinAccountView($builder, 'approved_by', 'ab');

        // Filters
        $this->filterParam($request, $builder, 'billing_status', 'billing_status');
        $this->filterParam($request, $builder, 'bill_type', 'bill_type');
        $this->filterParam($request, $builder, 'payment_method', 'payment_method');

        $builder->where("{$this->table}.deleted_at IS NULL");
        $builder->orderBy("{$this->table}.id", 'DESC');

        return $builder;
    }

    /**
     * DataTable action buttons
     */
    public function buttons($permissions)
    {
        $id         = $this->primaryKey;
        $dropdown   = false;
        $closureFun = function ($row) use ($id, $permissions, $dropdown) {
            $buttons = dt_button_actions($row, $id, $permissions, $dropdown);

            if (check_permissions($permissions, 'APPROVE') && $row['status'] === 'pending') {
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? 'Approve' : '',
                    'button'    => 'btn-primary',
                    'icon'      => 'fas fa-check-circle',
                    'condition' => dt_status_onchange($row[$id], 'approve', $row['status'], 'Billing Invoice'),
                ], $dropdown);
            }

            if ($row['status'] === 'approved') {
                if (
                    in_array($row['billing_status'], ['pending', 'overdue']) &&
                    check_permissions($permissions, 'MARK_PAID')
                ) {
                    $onclick = <<<EOF
                        onclick="edit({$row[$id]}, '{$row['billing_status']}')" title="Mark as Paid"
                    EOF;
                    $buttons .= dt_button_html([
                        'text'      => $dropdown ? 'Mark as Paid' : '',
                        'button'    => 'btn-success',
                        'icon'      => 'fas fa-ruble-sign',
                        'condition' => $onclick,
                    ], $dropdown);
                }

                if (check_permissions($permissions, 'PRINT')) {
                    $print_url = url_to('finance.billing_invoice_order_forms.print', $row[$id]);
                    $buttons .= <<<EOF
                        <a href="$print_url" class="btn btn-dark btn-sm" target="_blank"><i class="fas fa-print"></i></a>
                    EOF;
                }
            }

            return dt_buttons_dropdown($buttons);
        };

        return $closureFun;
    }

    /**
     * DataTable status formatter
     */
    public function dtStatusFormat()
    {
        $closureFun = function ($row) {
            $text    = ucwords($row['status']);
            $color   = dt_status_color($row['status']);

            return text_badge($color, $text);
        };

        return $closureFun;
    }

    /**
     * DataTable billing status formatter
     */
    public function dtBillingStatusFormat()
    {
        $closureFun = function ($row) {
            $text    = ucwords($row['billing_status']);
            $color   = dt_status_color($row['billing_status']);

            return text_badge($color, $text);
        };

        return $closureFun;
    }
}
