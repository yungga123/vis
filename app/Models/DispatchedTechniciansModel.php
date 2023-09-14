<?php

namespace App\Models;

use CodeIgniter\Model;

class DispatchedTechniciansModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'dispatched_technicians';
    protected $tableEmployees   = 'employees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'dispatch_id',
        'employee_id',
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

    // Columns to get employee name
    public function disTechGetEmployeeName($join = false)
    {
        if ($join) {
            return "
                {$this->table}.employee_id, 
                CONCAT({$this->tableEmployees}.firstname, ' ', {$this->tableEmployees}.lastname) AS techinician
            ";
        }

        return "{$this->table}.employee_id";
    }

    // Join with dispatch table
    public function disTechJoinDispatch($builder, $joinType = 'left')
    {
        $builder->join($this->table, "{$this->table}.dispatch_id = dispatch.id", $joinType);
    }

    // Join with employees table
    public function disTechJoinEmployees($builder)
    {
        $builder->join($this->tableEmployees, "{$this->table}.employee_id = {$this->tableEmployees}.id");
    }

    // Get dispatched technicians by dispatch_id
    public function getDispatchedTechnicians($dispatch_id, $join = false)
    {
        $builder = $this->select($this->disTechGetEmployeeName($join));
        if ($join) $this->disTechJoinEmployees($builder);

        $builder->where('dispatch_id', $dispatch_id);
        return $builder->findAll();
    }

    // Delete dispatched technicians
    public function deleteDispatchedTechnicians($dispatch_id)
    {
        $this->where('dispatch_id', $dispatch_id)->delete();
    }

    // Save dispatched technicians
    public function saveDispatchedTechnicians($dispatch_id, $technicians)
    {
        $data = [];
        foreach ($technicians as $technician) {
            $data[] = [
                'dispatch_id' => $dispatch_id,
                'employee_id' => $technician
            ];
        }
 
        $this->deleteDispatchedTechnicians($dispatch_id);
        $this->insertBatch($data);
    }
}
