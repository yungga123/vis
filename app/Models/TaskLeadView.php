<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskLeadView extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'task_lead_booked';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
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
    protected $allowCallbacks = false;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // DataTable
    public function noticeTable()
    {
        $userRole   = session('access_level');
        $builder    = $this->db->table($this->table)->select('*');

        if ($userRole === AAL_USER) {
            $builder->where('employee_id', session('employee_id'));
        }

        $builder->where('deleted_at IS NULL')->orderBy('id', 'desc');
        return $builder;
    }

    public function dtDetails()
    {
        $custom = function($row) {
            return <<<EOF
                <p><strong>Closed Deal:</strong> {$row['close_deal_date']}</p> 
                <p><strong>Branch:</strong> {$row['branch_name']}</p> 
                <p><strong>Project:</strong> {$row['project']}</p> 
            EOF;
        };
        
        return $custom;
    }
    
    public function buttons()
    {
        $closureFun = function($row) {
            return <<<EOF
                <a href="#" class="btn btn-info" title="View more details"><i class="fas fa-eye"></i> View</a> 
            EOF;
        };

        return $closureFun;
    }
}
