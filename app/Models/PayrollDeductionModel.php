<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollDeductionModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'payroll_deductions';
    protected $primaryKey       = 'payroll_id';
    protected $useAutoIncrement = false;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'payroll_id',
        'days_absent',
        'days_absent_amt',
        'hours_late',
        'hours_late_amt',
        'addt_rest_days',
        'addt_rest_days_amt',
        'govt_sss',
        'govt_pagibig',
        'govt_philhealth',
        'withholding_tax',
        'cash_advance',
        'others',
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
}
