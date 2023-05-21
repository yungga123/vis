<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomersVtModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'customers_vt';
    protected $view             = 'customervt_view';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "forecast",
        "customer_name", 
        "contact_person", 
        "address_province",
        "address_city", 
        "address_brgy", 
        "address_sub", 
        "contact_number", 
        "email_address", 
        "source", 
        "notes",
        "referred_by",
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        "forecast" => 'required',
        "customer_name" => 'required|max_length[500]',
        "contact_person" => 'required|max_length[500]',
        "address_province" => 'required|max_length[500]',
        "address_city" => 'required|max_length[500]',
        "address_brgy" => 'required|max_length[500]',
        "address_sub" => 'required|max_length[500]',
        "contact_number" => 'required|max_length[500]',
        "email_address" => 'max_length[500]',
        "source" => 'max_length[100]',
        "notes" => 'required|max_length[100]'
    ];
    protected $validationMessages   = [
        "forecast" => [
            "required" => "Please select if YES or NO"
        ],
        "customer_name" => [
            "required" => "Customer Name is required.",
            "max_length" => "Max length is 500."
        ],
        "contact_person" => [
            "required" => "Contact Person is required.",
            "max_length" => "Max length is 500.",
            
        ],
        "address_province" => [
            "required" => "Province is required.",
            "max_length" => "Max length is 500.",
            
        ],
        "address_city" => [
            "required" => "City is required.",
            "max_length" => "Max length is 500.",
           
        ],
        "address_brgy" => [
            "required" => "Barangay is required.",
            "max_length" => "Max length is 500.",
            
        ],
        "address_sub" => [
            "required" => "Detailed Address is required.",
            "max_length" => "Max length is 500."
        ],
        "contact_number" => [
            "required" => "Contact Number is required.",
            "max_length" => "Max length is 500.",
            
        ],
        "email_address" => [
            "max_length" => "Max length is 500."
        ],
        "source" => [
            "max_length" => "Max length is 100.",
            
        ],
        "notes" => [
            "required" => "Additional Notes is required.",
            "max_length" => "Max length is 100.",
            
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

    public function getCustomerName($id)
    {
        return $this->select('customer_name')->find($id);
    }

    public function noticeTable() 
    {
        $builder = $this->db->table($this->view);
        $builder->select("*");
        return $builder;
    }

    public function buttons($permissions)
    {
        $id = 'id';
        $closureFun = function($row) use($id, $permissions) {
            if (is_admin()) {
                return <<<EOF
                    <button class="btn btn-sm btn-info" onclick="branchCustomervtRetrieve({$row["$id"]})" title="View Branch"><i class="fas fa-eye"></i> </button>

                    <button class="btn btn-sm btn-success" onclick="addBranch({$row["$id"]}, '{$row["customer_name"]}')" title="Add Branch"><i class="fas fa-plus-square"></i> </button> 

                    <button class="btn btn-sm btn-warning" onclick="edit({$row["$id"]})" title="Edit"><i class="fas fa-edit"></i> </button> 

                    <button class="btn btn-sm btn-danger" onclick="remove({$row["$id"]})" title="Delete"><i class="fas fa-trash"></i></button>  
                EOF;
            }

            $view = <<<EOF
                <button class="btn btn-sm btn-info" onclick="branchCustomervtRetrieve({$row["$id"]})" title="View Branch"><i class="fas fa-eye"></i> </button>
            EOF;

            $add = '<button class="btn btn-sm btn-success" title="Cannot add" disabled><i class="fas fa-plus-square"></i> </button>';

            if (check_permissions($permissions, 'ADD') && !is_admin()) {
                $add = <<<EOF
                    <button class="btn btn-sm btn-success" onclick="addBranch({$row["$id"]}, '{$row["customer_name"]}')" title="Add Branch"><i class="fas fa-plus-square"></i> </button> 
                EOF;
            }

            $edit = '<button class="btn btn-sm btn-warning" title="Cannot edit" disabled><i class="fas fa-edit"></i> </button>';

            if (check_permissions($permissions, 'EDIT') && !is_admin()) {
                $edit = <<<EOF
                    <button class="btn btn-sm btn-warning" onclick="edit({$row["$id"]})" title="Edit"><i class="fas fa-edit"></i> </button> 
                EOF;
            }

            $delete = '<button class="btn btn-sm btn-danger" title="Cannot delete" disabled><i class="fas fa-trash"></i> </button>';

            if (check_permissions($permissions, 'DELETE') && !is_admin()) {
                $delete = <<<EOF
                    <button class="btn btn-sm btn-danger" onclick="remove({$row["$id"]})" title="Delete"><i class="fas fa-trash"></i></button>  
                EOF;
            }

            return $view . $add . $edit . $delete;
                        
        };

        return $closureFun;
    }
}
