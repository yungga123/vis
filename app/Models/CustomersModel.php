<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomersModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
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
        "customer_name" => 'required|max_length[500]|alpha_numeric_space',
        "contact_person" => 'required|max_length[500]|alpha_numeric_space',
        "address_province" => 'required|max_length[500]|alpha_numeric_space',
        "address_city" => 'required|max_length[500]|alpha_numeric_space',
        "address_brgy" => 'required|max_length[500]|alpha_numeric_space',
        "address_sub" => 'required|max_length[500]',
        "contact_number" => 'required|max_length[500]|alpha_numeric_space',
        "email_address" => 'max_length[500]',
        "source" => 'max_length[100]|alpha_numeric_space',
        "notes" => 'required|max_length[100]|alpha_numeric_space'
    ];
    protected $validationMessages   = [
        "customer_name" => [
            "required" => "Customer Name is required.",
            "max_length" => "Max length is 500.",
            "alpha_numeric_space" => "Customer Name must only contain alpha-numeric characters."
        ],
        "contact_person" => [
            "required" => "Contact Person is required.",
            "max_length" => "Max length is 500.",
            "alpha_numeric_space" => "Contact Person must only contain alpha-numeric characters."
        ],
        "address_province" => [
            "required" => "Province is required.",
            "max_length" => "Max length is 500.",
            "alpha_numeric_space" => "Province must only contain alpha-numeric characters."
        ],
        "address_city" => [
            "required" => "City is required.",
            "max_length" => "Max length is 500.",
            "alpha_numeric_space" => "City must only contain alpha-numeric characters."
        ],
        "address_brgy" => [
            "required" => "Barangay is required.",
            "max_length" => "Max length is 500.",
            "alpha_numeric_space" => "Barangay must only contain alpha-numeric characters."
        ],
        "address_sub" => [
            "required" => "Detailed Address is required.",
            "max_length" => "Max length is 500."
        ],
        "contact_number" => [
            "required" => "Contact Number is required.",
            "max_length" => "Max length is 500.",
            "alpha_numeric_space" => "Contact Number must only contain alpha-numeric characters."
        ],
        "email_address" => [
            "max_length" => "Max length is 500."
        ],
        "source" => [
            "max_length" => "Max length is 100.",
            "alpha_numeric_space" => "Source of contact must only contain alpha-numeric characters."
        ],
        "notes" => [
            "required" => "Additional Notes is required.",
            "max_length" => "Max length is 100.",
            "alpha_numeric_space" => "Additional Notes must only contain alpha-numeric characters."
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


    
}
