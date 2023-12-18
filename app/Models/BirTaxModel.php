<?php

namespace App\Models;

use CodeIgniter\Model;

class BirTaxModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bir_taxes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'compensation_range_start',
        'compensation_range_end',
        'fixed_tax_amount',
        'compensation_level',
        'tax_rate',
        'below_or_above',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'compensation_range_start'     => [
            'rules' => 'required',
            'label' => 'monthly start',
        ],
        'compensation_range_end'     => [
            'rules' => 'required',
            'label' => 'monthly end',
        ],
        'fixed_tax_amount'   => [
            'rules' => 'required',
            'label' => 'fixed tax amount',
        ],
        'compensation_level'   => [
            'rules' => 'required',
            'label' => 'compensation level',
        ],
        'tax_rate'   => [
            'rules' => 'required',
            'label' => 'tax rate',
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
     */
    public function fetch(string $id, string|array $columns = ''): array|null
    {
        $columns = $columns ? $columns : array_merge([$this->primaryKey], $this->allowedFields);
        
        $this->select($columns);
        $this->where("{$this->table}.id", $id);

        return $this->first();
    }

    /**
     * For fetching multiple data
     * 
     * @param string|array $columns
     */
    public function fetchAll(array $id = [], $columns = '', int $limit = 0, int $offset = 0): array
    {
        $columns = $columns ? $columns : array_merge([$this->primaryKey], $this->allowedFields);
        
        $this->select($columns);

        if (! empty($id)) 
            $this->whereIn("{$this->table}.id", $id);

        return $this->findAll($limit, $offset);
    }
}
