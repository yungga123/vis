<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;
use App\Traits\FilterParamTrait;

class BillingInvoiceModel extends Model
{
    /* Declare trait here to use */
    use HRTrait, FilterParamTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'billing_invoices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'status',
        'tasklead_id',
        'billing_status',
        'due_date',
        'bill_type',
        'billing_amount',
        'payment_method',
        'amount_paid',
        'paid_at',
        'paid_by',
        'attention_to',
        'with_vat',
        'vat_amount',
        'overdue_interest',
        'grand_total',
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
        'tasklead_id'     => [
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
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedByValue'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Custom variables
    // Restrict edit/delete action for this statuses
    protected $restrictedStatuses = ['paid', 'approved'];

    /**
     * Set the value for created_by before inserting
     */
    protected function setCreatedByValue(array $data)
    {
        $data['data']['created_by'] = session('username');
        return $data;
    }

    /**
     * For counting records
     */
    public function countRecords($param = null)
    {
        $builder = $this->where('deleted_at IS NULL');

        if (! $param) return $builder->countAllResults();

        return $builder->where('billing_status', strtolower($param))->countAllResults();        
    }

    /**
     * Make amount_paid required
     */
    public function makeAmountPaidRequired()
    {
        $this->validationRules['amount_paid']['rules'] = 'required|numeric';
    }

    /**
     * Default columns for fetching records
     */
    public function columns(bool $joinTlV = false)
    {
        $columns = "
            {$this->table}.id,
            {$this->table}.status,
            {$this->table}.tasklead_id,
            {$this->table}.due_date,
            {$this->table}.bill_type,
            {$this->table}.billing_amount,
            {$this->table}.billing_status,
            {$this->table}.payment_method,
            {$this->table}.amount_paid,
            {$this->table}.paid_at,
            {$this->table}.attention_to,
            {$this->table}.with_vat,
            {$this->table}.vat_amount,
            {$this->table}.overdue_interest,
            {$this->table}.grand_total,
            {$this->table}.created_by,
            {$this->table}.created_at,
            {$this->table}.approved_by,
            {$this->table}.approved_at
        ";

        if ($joinTlV) {
            $tlVModel   = new TaskLeadView();
            $columns    .= ",
                {$tlVModel->table}.quotation_num AS quotation,
                {$tlVModel->table}.customer_name AS client,
                {$tlVModel->table}.employee_name AS manager,
                {$tlVModel->table}.tasklead_type AS quotation_type,
                {$tlVModel->table}.project,
                {$tlVModel->table}.project_amount
            ";
        }

        return $columns;
    }

    /**
     * Join task_lead_booked
     */
    public function joinBookedTasklead($builder = null, $model = null, $type = 'left')
    {      
        $model ?? $model = new TaskLeadView();
        ($builder ?? $this)->join($model->table, "{$this->table}.tasklead_id = {$model->table}.id", $type);

        return $this;
    }

    /**
     * For fetching single data
     */
    public function fetch(string|int $id, bool $joinTlV = false): array|null
    {
        $columns = $this->columns($joinTlV);
        
        $this->select($columns);

        if ($joinTlV) $this->joinBookedTasklead();

        $this->where("{$this->table}.id", $id);
        $this->where("{$this->table}.deleted_at IS NULL");

        return $this->first();
    }

    /**
     * For fetching multiple data
     */
    public function fetchAll(array $id = [], bool $joinTlV = false, int $limit = 0, int $offset = 0): array|null
    {
        $columns = $this->columns($joinTlV);
        
        $this->select($columns);

        if ($joinTlV) $this->joinBookedTasklead();

        if (! empty($id)) {
            $this->whereIn("{$this->table}.id", $id);
        }

        $this->where("{$this->table}.deleted_at IS NULL");

        return $this->findAll($limit, $offset);
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

        if (! empty($overdues)) {
            $ids    = format_results($overdues, 'id', '', true);
            $data   = ['billing_status' => 'overdue'];

            $self->set($data)->whereIn('id', $ids)->update();
        }
    }

    /**
     * For dataTables
     */
    public function noticeTable($request) 
    {
        $tlVModel   = new TaskLeadView();
        $builder    = $this->db->table($this->table);
        $columns    = "
            {$this->table}.id,
            {$this->table}.status,
            {$this->table}.tasklead_id,
            {$tlVModel->table}.quotation_num AS quotation,
            {$tlVModel->table}.customer_name AS client,
            {$tlVModel->table}.employee_name AS manager,
            {$tlVModel->table}.tasklead_type AS quotation_type,
            ".dt_sql_date_format("{$this->table}.due_date")." AS due_date,
            {$this->table}.bill_type,
            ".dt_sql_number_format("{$this->table}.billing_amount")." AS billing_amount,
            ".dt_sql_number_format("{$this->table}.overdue_interest")." AS overdue_interest,
            {$this->table}.billing_status,
            {$this->table}.payment_method,
            ".dt_sql_number_format("{$this->table}.amount_paid")." AS amount_paid,
            ".dt_sql_datetime_format("{$this->table}.paid_at")." AS paid_at,
            {$this->table}.attention_to,
            IF({$this->table}.with_vat = 0, 'NO', 'YES') AS with_vat,
            ".dt_sql_number_format("{$this->table}.vat_amount")." AS vat_amount,
            cb.employee_name AS created_by,
            ab.employee_name AS approved_by,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at,
            ".dt_sql_datetime_format("{$this->table}.approved_at")." AS approved_at
        ";

        $builder->select($columns);

        // Join with other tables
        $this->joinBookedTasklead($builder, $tlVModel);
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
        $closureFun = function($row) use($id, $permissions, $dropdown) {
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
                    $print_url = url_to('finance.billing_invoice.print', $row[$id]);
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
        $closureFun = function($row) {
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
        $closureFun = function($row) {
            $text    = ucwords($row['billing_status']);
            $color   = dt_status_color($row['billing_status']);

            return text_badge($color, $text);
        };
        
        return $closureFun;
    }
}
