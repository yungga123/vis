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
        'status',
        'due_date',
        'bill_type',
        'billing_amount',
        'payment_method',
        'amount_paid',
        'paid_at',
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

    /**
     * Set the value for created_by before inserting
     */
    protected function setCreatedByValue(array $data)
    {
        $data['data']['created_by'] = session('username');
        return $data;
    }

    /**
     * Make amount_paid required
     */
    public function makeAmountPaidRequired()
    {
        $this->validationRules['amount_paid']['rules'] = 'required|numeric';
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
     * For dataTables
     */
    public function noticeTable($request) 
    {
        $builder = $this->db->table($this->table);
        $columns = "
            {$this->table}.id,
            {$this->table}.tasklead_id,
            {$this->table}.status,
            ".dt_sql_date_format("{$this->table}.due_date")." AS due_date,
            {$this->table}.bill_type,
            {$this->table}.billing_amount,
            {$this->table}.payment_method,
            {$this->table}.amount_paid,
            ".dt_sql_datetime_format("{$this->table}.paid_at")." AS paid_at,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at,
        ";

        $builder->select($this->_columns(true, true, true));

        // Join with other tables
        $this->joinView($builder);
        $this->joinSchedule($builder);
        $this->joinAccountView($builder, 'created_by', 'cb');

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
                $print_url = site_url('dispatch/print/') . $row[$id];
                $buttons .= <<<EOF
                    <a href="$print_url" class="btn btn-success btn-sm" target="_blank"><i class="fas fa-print"></i></a>
                EOF;
            }

            return $buttons;
        };
        
        return $closureFun;
    }
}
