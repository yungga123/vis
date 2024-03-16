<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerSupportSpecialistModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'customer_support_specialists';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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
     * Get specialists
     */
    public function getSpecialists($customer_support_id) 
    {
        $this->where("{$this->table}.customer_support_id", $customer_support_id);

        return $this->findAll();
    }

    /**
     * Save specialists
     */
    public function saveSpecialists($specialists, $customer_support_id) 
    {
        if (! empty($specialists)) {
            $arr = [];
            
            // Delete first
            $this->deleteSpecialists($customer_support_id);

            if (is_array($specialists)) {
                foreach ($specialists as $value) {
                    $arr[] = [
                        'customer_support_id'   => $customer_support_id,
                        'employee_id'           => $value,
                    ];
                }
            } else {
                $arr[] = [
                    'customer_support_id'   => $customer_support_id,
                    'employee_id'           => $specialists,
                ];
            }

            if (! empty($arr)) $this->db->table($this->table)->insertBatch($arr);
        }
    }

    /**
     * Delete specialists
     */
    public function deleteSpecialists($customer_support_id) 
    {
        $this->where('customer_support_id', $customer_support_id)->delete();
    }

    /**
     * Join with employees_view
     */
    public function joinEmployeesView($builder = null, $mode = null) 
    {
        $builder = $builder ?? $this;
        $model ??= new EmployeeViewModel();
        
        $builder->join($model->table, "{$this->table}.employee_id = {$model->table}.employee_id", 'left');

        return $builder;
    }
}
