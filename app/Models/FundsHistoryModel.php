<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;
use App\Traits\FilterParamTrait;

class FundsHistoryModel extends Model
{
    /* Declare trait here to use */
    use HRTrait, FilterParamTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'funds_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'billing_invoice_id',
        'transaction_type',
        'transaction_amount',
        'current_funds',
        'coming_from',
        'expenses',
        'remarks',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'transaction_type'     => [
            'rules' => 'required',
            'label' => 'type',
        ],
        'transaction_amount'   => [
            'rules' => 'required|numeric',
            'label' => 'amount',
        ],
        'current_funds'     => [
            'rules' => 'required',
            'label' => 'current funds',
        ],
        'coming_from'   => [
            'rules' => 'required|max_length[100]',
            'label' => 'coming from',
        ],
        'expenses'   => [
            'rules' => 'if_exist|max_length[100]',
            'label' => 'purpose',
        ],
        'remarks'   => [
            'rules' => 'permit_empty|string|max_length[200]',
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
     * For dataTables
     */
    public function noticeTable($request) 
    {
        $builder    = $this->db->table($this->table);
        $compute    = "
            IF({$this->table}.transaction_type = 'outgoing', {$this->table}.current_funds - {$this->table}.transaction_amount, {$this->table}.current_funds + {$this->table}.transaction_amount)
        ";
        $columns    = "
            {$this->table}.id,
            {$this->table}.transaction_type,
            ".dt_sql_number_format("{$this->table}.transaction_amount")." AS transaction_amount,
            ".dt_sql_number_format("{$this->table}.current_funds")." AS previous_funds,
            ".dt_sql_number_format("{$compute}")." AS current_funds,
            {$this->table}.coming_from,
            {$this->table}.expenses,
            {$this->table}.remarks,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at
        ";

        $builder->select(new \CodeIgniter\Database\RawSql($columns));

        // Join with other tables
        $this->joinAccountView($builder, 'created_by', 'cb');

        // Filters
        $this->filterParam($request, $builder, 'transaction_type', 'transaction_type');
        $this->filterParam($request, $builder, 'coming_from', 'coming_from');
        $this->filterParam($request, $builder, 'expenses', 'expenses');

        $builder->where("{$this->table}.deleted_at IS NULL");
        $builder->orderBy("{$this->table}.id", 'DESC');

        return $builder;
    }

    /**
     * DataTable transaction_type formatter
     */
    public function dtTransactionTypeFormat()
    {
        $closureFun = function($row) {
            $text    = ucwords($row['transaction_type']);
            $color   = $row['transaction_type'] === 'incoming' ? 'success' : 'danger';

            return text_badge($color, $text);
        };
        
        return $closureFun;
    }
}
