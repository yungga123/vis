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
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "quarter", 
        "status", 
        "customer_id",
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
        "quotation_num" => "required|max_length[100]",
        "quarter" => 'required',
        "status" => 'required|is_natural_no_zero|max_length[3]|less_than_equal_to[100]',
        "customer_id" => 'required',
        "project" => 'required|max_length[500]',
        "project_amount" => 'required|decimal|max_length[18]',
        "remark_next_step" => 'required'
    ];
    protected $validationMessages   = [
        "quotation_num" => [
            "required" => "This field is required.",
            "max_length" => "Max of 100 characters."
        ],
        "quarter" => [
            "required" => "This field is required.",
        ],
        "status" => [
            "required" => "This field is required.",
            "is_natural_no_zero" => "Please input 1-100 only.",
            "max_length[3]" => "Max of 3 characters.",
            "less_than_equal_to" => "Please input 1-100 only."
        ],
        "customer_id" => [
            "required" => "This field is required.",
        ],
        "project" => [
            "required" => "This field is required.",
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
}
