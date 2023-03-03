<?php

namespace App\Models;

use CodeIgniter\Model;


class TaskLeadModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tasklead';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "employee_id",
        "quarter", 
        "status",
        "customer_type",
        "existing_customer",
        "customer_id",
        "branch_id",
        "project", 
        "project_amount", 
        "quotation_num", 
        "forecast_close_date",
        "remark_next_step", 
        "close_deal_date", 
        "project_start_date", 
        "project_finish_date"
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        "customer_type" => ["label" => "Customer Type", "rules" => "required"],
        "existing_customer" => ["label" => "Existing Customer", "rules" => "required"],
        "customer_id" => ["label" => "Customer", "rules" => "required"],
        "quotation_num" => "max_length[100]",
        "quarter" => 'required',
        "status" => 'required',
        "customer_id" => 'required',
        "project" => 'max_length[500]',
        "project_amount" => 'permit_empty|decimal|max_length[18]',
        "remark_next_step" => 'required',
    ];
    protected $validationMessages   = [
        "quotation_num" => [
            "max_length" => "Max of 100 characters."
        ],
        "quarter" => [
            "required" => "This field is required.",
        ],
        "status" => [
            "required" => "This field is required."
        ],
        "customer_id" => [
            "required" => "This field is required.",
        ],
        "project" => [
            "max_length" => "Maximum of 500 characters."
        ],
        "project_amount" => [
            "required" => "This field is required.",
            "decimal" => "Numbers or decimals only.",
            "max_length" => "Maximum of 18 characters."
        ],
        "remark_next_step" => [
            "required" => "This field is required.",
        ]
    ];
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

    public function noticeTable(){
        $db      = \Config\Database::connect();
        $builder = $db->table('task_lead');
        $builder->select('*');
        return $builder;
    }

    public function noticeTableExistingCustomer(){
        $db      = \Config\Database::connect();
        $builder = $db->table('task_lead_existing_customer');
        $builder->select('*');
        return $builder;
    }


    public function noticeTableWhere($employee_id){
        $db      = \Config\Database::connect();
        $builder = $db->table('task_lead')->where('employee_id',$employee_id);
        $builder->select('*');
        return $builder;
    }

    public function noticeTableWhereExistingCustomer($employee_id){
        $db      = \Config\Database::connect();
        $builder = $db->table('task_lead_existing_customer')->where('employee_id',$employee_id);
        $builder->select('*');
        return $builder;
    }

    public function noticeTableBooked(){
        $db      = \Config\Database::connect();
        $builder = $db->table('task_lead_booked');
        $builder->select('*');
        return $builder;
    }
    public function noticeTableBookedWhere($employee_id){
        $db      = \Config\Database::connect();
        $builder = $db->table('task_lead_booked')->where('employee_id',$employee_id);
        $builder->select('*');
        return $builder;
    }

    // public function buttonEdit(){
    //     $closureFun = function($row){
    //         return <<<EOF
    //             <button class="btn btn-block btn-danger btn-xs delete-tasklead" data-toggle="modal" data-target="#modal-delete-tasklead" data-id="{$row['id']}"><i class="fas fa-trash"></i> Delete</button>
    //             <button class="btn btn-block btn-success btn-xs update-tasklead" data-toggle="modal" data-target="#modal-update-tasklead" data-id="{$row['id']}" data-percent="{$row['status']}"><i class="far fa-arrow-alt-circle-up"></i> Forecast</button>
    //         EOF; 
    //     };
    //     return $closureFun;
    // }

    public function buttons()
    {
        $closureFun = function($row) {
            return <<<EOF
                <button class="btn btn-sm btn-success" onclick="edit({$row["id"]})"  data-toggle="modal" data-target="#modal_tasklead" title="Update Tasklead"><i class="fas fa-arrow-up"></i> </button> 
                <button class="btn btn-sm btn-danger" onclick="remove({$row["id"]})" title="Delete"><i class="fas fa-trash"></i></button> 
            EOF;
        };
        return $closureFun;
    }
}
