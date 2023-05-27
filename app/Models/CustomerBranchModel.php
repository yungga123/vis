<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerBranchModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'customer_branch';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "customer_id",
        "branch_name",
        "address_province",
        "address_city",
        "address_brgy",
        "address_sub",
        "contact_number",
        "contact_person",
        "email_address",
        "notes"
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        "customer_id" => 'required',
        "branch_name" => 'required|max_length[500]',
        "address_province" => 'required|max_length[500]',
        "address_city" => 'required|max_length[500]',
        "address_brgy" => 'required|max_length[500]',
        "address_sub" => 'permit_empty|max_length[500]',
        "contact_number" => 'required|max_length[500]',
        "contact_person" => 'required|max_length[500]',
        "email_address" => 'permit_empty|valid_email|max_length[500]',
        "notes" => 'required|max_length[500]',
    ];
    protected $validationMessages   = [
        "customer_id" => [
            "required" => "This field is required"
        ],
        "branch_name" => [
            "required" => "This field is required",
            "max_length" => "Max length is 500."
        ],
        "address_province" => [
            "required" => "This field is required",
            "max_length" => "Max length is 500."
        ],
        "address_city" => [
            "required" => "This field is required",
            "max_length" => "Max length is 500."
        ],
        "address_brgy" => [
            "required" => "This field is required",
            "max_length" => "Max length is 500."
        ],
        "address_sub" => [
            "required" => "This field is required",
            "max_length" => "Max length is 500."
        ],
        "contact_number" => [
            "required" => "This field is required",
            "max_length" => "Max length is 500."
        ],
        "contact_person" => [
            "required" => "This field is required",
            "max_length" => "Max length is 500."
        ],
        "email_address" => [
            "valid_email" => "Please enter valid email.",
            "max_length" => "Max length is 500."
        ],
        "notes" => [
            "required" => "This field is required",
            "max_length" => "Max length is 500."
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

    public function noticeTable($customers_id) 
    {
        $builder = $this->db->table('customer_view_branch');
        $builder->where('customer_id', $customers_id);
        $builder->select("*");

        return $builder;
    }

    public function buttons()
    {
        $id = $this->primaryKey;
        $closureFun = function($row) use($id) {
            return <<<EOF
                <button class="btn btn-sm btn-warning" onclick="editBranch({$row["$id"]})" title="Edit" data-toggle="modal" data-target="#modal_branchcustomer"><i class="fas fa-edit"></i> </button> 

                <button class="btn btn-sm btn-danger" onclick="removeBranch({$row["$id"]})" title="Delete"><i class="fas fa-trash"></i></button>                
            EOF; 
        };

        return $closureFun;
    }
}
