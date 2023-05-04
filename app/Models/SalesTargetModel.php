<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesTargetModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sales_target';
    protected $view             = 'sales_target_view';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sales_id',
        'q1_target',
        'q2_target',
        'q3_target',
        'q4_target',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'sales_id'      => [
            'label' => 'Sales ID',
            'rules' => 'required'
        ],
        'q1_target'     => [
            'label' => 'Q1 Target',
            'rules' => 'required|numeric'
        ],
        'q2_target'     => [
            'label' => 'Q2 Target',
            'rules' => 'required|numeric'

        ],
        'q3_target'     => [
            'label' => 'Q3 Target',
            'rules' => 'required|numeric'
        ],
        'q4_target'     => [
            'label' => 'Q4 Target',
            'rules' => 'required|numeric'
        ],
    ];
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

    // For dataTables
    public function noticeTable() 
    {
        $builder = $this->db->table($this->view);
        $builder->select("*");

        return $builder;
    }

}
