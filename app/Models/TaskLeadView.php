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
                <p><strong>Project Start:</strong> {$row['project_start_date']}</p>
                <p><strong>Project Finish:</strong> {$row['project_finish_date']}</p>
            EOF;
        };
        
        return $custom;
    }

    public function customerDetails()
    {
        $custom = function($row) {

            $branch = $row['branch_name'] ? $row['branch_name'] : "<span class='text-danger'><i>Not Set</i></span>";

            return <<<EOF
                {$row['customer_name']}
                <br>
                <small class="text-muted">{$row['customer_type']} Customer</small>
                <br>
                <small class="text-muted">Branch: {$branch}</small>
                <p><small class="text-muted">Project: {$row['project']}</small></p>
            EOF;
        };
        
        return $custom;
    }
    
    public function buttons()
    {
        $closureFun = function($row) {
            return <<<EOF
                <a href="#" class="btn btn-info" title="View more details" data-toggle="modal" data-target="#modal-booked-details" onclick="getBookedDetails({$row['id']})"><i class="fas fa-eye"></i> View</a> 
            EOF;
        };

        return $closureFun;
    }
}
