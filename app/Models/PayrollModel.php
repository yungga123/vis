<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'payroll';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employee_id',
        'cutoff_start',
        'cutoff_end',
        'gross_pay',
        'net_pay',
        'salary_type',
        'basic_salary',
        'cutoff_pay',
        'daily_rate',
        'hourly_rate',
        'working_days',
        'notes',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'employee_id'     => [
            'rules' => 'required|max_length[100]',
            'label' => 'employee name',
        ],
        'cutoff_start'   => [
            'rules' => 'required',
            'label' => 'cut-off start date',
        ],
        'cutoff_end'   => [
            'rules' => 'required',
            'label' => 'cut-off end date',
        ],
        'gross_pay'   => [
            'rules' => 'required',
            'label' => 'gross pay',
        ],
        'net_pay'   => [
            'rules' => 'required',
            'label' => 'net pay',
        ],
        'salary_type'   => [
            'rules' => 'required',
            'label' => 'salary type',
        ],
        'basic_salary'   => [
            'rules' => 'required',
            'label' => 'basic salary',
        ],
        'cutoff_pay'   => [
            'rules' => 'required',
            'label' => 'cut-off pay',
        ],
        'daily_rate'   => [
            'rules' => 'required',
            'label' => 'daily rate',
        ],
        'hourly_rate'   => [
            'rules' => 'required',
            'label' => 'hourly rate',
        ],
        'notes'   => [
            'rules' => 'permit_empty|max_length[500]',
            'label' => 'notes',
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
     * For fetching single data
     * 
     * @param bool $byPKId  Fetch by primary id - default true
     */
    public function fetch(string|int $id, string|array $columns = '', $byPKId = true): array|null
    {
        $columns    = $columns ? $columns :  [$this->primaryKey] + $this->allowedFields;
        $field      = $byPKId ? 'id' : 'employee_id';
        
        $this->select($columns);
        $this->where("{$this->table}.{$field}", $id);
        $this->where("{$this->table}.deleted_at IS NULL");

        return $this->first();
    }

    /**
     * For fetching multiple data
     * 
     * @param string|array $columns
     * @param bool $byPKId  Fetch by primary id - default true
     */
    public function fetchAll(array $id = [], $columns = '', $byPKId = true, int $limit = 0, int $offset = 0): array
    {
        $columns = $columns ? $columns : [$this->primaryKey] + $this->allowedFields;
        
        $this->select($columns);

        if (! empty($id)) {
            $field = $byPKId ? 'id' : 'employee_id';
            $this->whereIn("{$this->table}.{$field}", $id);
        }

        $this->where("{$this->table}.deleted_at IS NULL");

        return $this->findAll($limit, $offset);
    }
}
