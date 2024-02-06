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
        'tasklead_id',
        'billing_status',
        'due_date',
        'bill_type',
        'billing_amount',
        'payment_method',
        'amount_paid',
        'paid_at',
        'attention_to',
        'with_vat',
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
            'rules' => 'required',
            'label' => 'task lead',
        ],
        'due_date'     => [
            'rules' => 'required',
            'label' => 'due date',
        ],
        'bill_type'   => [
            'rules' => 'required',
            'label' => 'bill type',
        ],
        'billing_amount'   => [
            'rules' => 'required|numeric',
            'label' => 'billing amount',
        ],
        'payment_method'   => [
            'rules' => 'required',
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
    protected $restrictedStatuses = ['paid'];

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
            {$this->table}.created_by,
            {$this->table}.created_at
        ";

        if ($joinTlV) {
            $tlVModel   = new TaskLeadView();
            $columns    .= ",
                {$tlVModel->table}.quotation_num AS quotation,
                {$tlVModel->table}.customer_name AS client,
                {$tlVModel->table}.employee_name AS manager,
                {$tlVModel->table}.tasklead_type AS quotation_type,
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
     * For dataTables
     */
    public function noticeTable($request) 
    {
        $tlVModel   = new TaskLeadView();
        $builder    = $this->db->table($this->table);
        $columns    = "
            {$this->table}.id,
            {$this->table}.tasklead_id,
            {$tlVModel->table}.quotation_num AS quotation,
            {$tlVModel->table}.customer_name AS client,
            {$tlVModel->table}.employee_name AS manager,
            {$tlVModel->table}.tasklead_type AS quotation_type,
            ".dt_sql_date_format("{$this->table}.due_date")." AS due_date,
            {$this->table}.bill_type,
            ".dt_sql_number_format("{$this->table}.billing_amount")." AS billing_amount,
            {$this->table}.billing_status,
            {$this->table}.payment_method,
            ".dt_sql_number_format("{$this->table}.amount_paid")." AS amount_paid,
            ".dt_sql_datetime_format("{$this->table}.paid_at")." AS paid_at,
            {$this->table}.attention_to,
            IF({$this->table}.with_vat = 0, 'NO', 'YES') AS with_vat,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at
        ";

        $builder->select($columns);

        // Join with other tables
        $this->joinBookedTasklead($builder, $tlVModel);
        $this->joinAccountView($builder, 'created_by', 'cb');

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
        $closureFun = function($row) use($id, $permissions) {
            $buttons = dt_button_actions($row, $id, $permissions, false);

            if (check_permissions($permissions, 'PRINT')) {
                $print_url = url_to('finance.billing_invoice.print', $row[$id]);
                $buttons .= <<<EOF
                    <a href="$print_url" class="btn btn-success btn-sm" target="_blank"><i class="fas fa-print"></i></a>
                EOF;
            }

            return $buttons;
        };
        
        return $closureFun;
    }

    /**
     * DataTable status formatter
     */
    public function dtStatusFormat()
    {
        $closureFun = function($row) {
            $text    = ucwords($row['billing_status']);
            $color   = dt_status_color($row['billing_status']);

            return text_badge($color, $text);
        };
        
        return $closureFun;
    }
}
