<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollEarningModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'payroll_earnings';
    protected $primaryKey       = 'payroll_id';
    protected $useAutoIncrement = false;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'payroll_id',
        'working_days_off',
        'working_days_off_amt',
        'over_time',
        'over_time_amt',
        'night_diff',
        'night_diff_amt',
        'regular_holiday',
        'regular_holiday_amt',
        'special_holiday',
        'special_holiday_amt',
        'service_incentive_leave',
        'service_incentive_leave_amt',
        'incentives',
        'commission',
        'thirteenth_month',
        'add_back',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * For fetching single data
     */
    public function fetch(string|int $id, string|array $columns = ''): array|null
    {
        $columns = $columns ? $columns :  $this->allowedFields;
        
        $this->select($columns);
        $this->where("{$this->table}.{$this->primaryKey}", $id);

        return $this->first();
    }

    /**
     * For fetching multiple data
     */
    public function fetchAll(array $id = [], string $columns = '', int $limit = 0, int $offset = 0): array
    {
        $columns = $columns ? $columns : $this->allowedFields;
        
        $this->select($columns);

        if (! empty($id)) {
            $this->whereIn("{$this->table}.{$this->primaryKey}", $id);
        }

        $this->where("{$this->table}.deleted_at IS NULL");

        return $this->findAll($limit, $offset);
    }

    /**
     * For checking data if exists
     */
    public function exists(string|int $id): bool
    {
        return ! empty($this->fetch($id, 'payroll_id'));
    }
}
