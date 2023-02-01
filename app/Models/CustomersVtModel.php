<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomersVtModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'customers_vt';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "customer_type",
        "customer_name", 
        "contact_person", 
        "address_province",
        "address_city", 
        "address_brgy", 
        "address_sub", 
        "contact_number", 
        "email_address", 
        "source", 
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
        "customer_type" => 'required',
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
        "customer_type" => [
            "required" => "Customer Type is required.",
            "max_length" => "Max length is 500."
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

    public function noticeTable($customer_type) {
        $db      = \Config\Database::connect();
        $builder = $db->table('customervt_view');
        $builder->select("*")->where('customer_type',$customer_type);
        return $builder;
    }

    public function buttonEdit(){
        $closureFun = function($row){

            $url_edit = site_url('edit-customervt/'.$row['id']);

            return <<<EOF
                <button class="btn btn-block btn-warning btn-xs btn-customer-edit" target="_blank" data-toggle="modal" data-target="#modal-edit-customervt" data-url="{$url_edit}"><i class="fas fa-edit"></i> Edit</a>
                <button class="btn btn-block btn-danger btn-xs delete-employee" data-toggle="modal" data-target="#modal-delete-customervt" data-id="{$row['id']}"><i class="fas fa-trash"></i> Delete</button>
            EOF; 
        };
        return $closureFun;
    }
}
