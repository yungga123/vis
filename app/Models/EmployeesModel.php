<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeesModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'employees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "employee_id",
        "firstname",
        "middlename",
        "lastname",
        "gender",
        "civil_status",
        "date_of_birth",
        "place_of_birth",
        "postal_code",
        "language",
        "address_province",
        "address_city",
        "address_brgy",
        "address_sub",
        "contact_number",
        "email_address",
        "sss_no",
        "tin_no",
        "philhealth_no",
        "pag_ibig_no",
        "educational_attainment",
        "course",
        "emergency_name",
        "emergency_contact_no",
        "emergency_address",
        "name_of_spouse",
        "spouse_contact_no",
        "no_of_children",
        "spouse_address",
        "position",
        "employment_status",
        "date_hired",
        "date_resigned"
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        "employee_id"               => "required|alpha_numeric|max_length[20]|is_unique[employees.employee_id]",
        "firstname"                 => "required|alpha_numeric_space|max_length[500]",
        "lastname"                  => "required|alpha_numeric_space|max_length[500]",
        "gender"                    => "required",
        "civil_status"              => "required|alpha_numeric_space|max_length[500]",
        "date_of_birth"             => "required",
        "place_of_birth"            => "required|max_length[500]",
        "postal_code"               => "required|numeric|max_length[500]",
        "language"                  => "required|max_length[500]",
        "address_province"          => "required|max_length[500]",
        "address_city"              => "required|max_length[500]",
        "address_brgy"              => "required|max_length[500]",
        "address_sub"               => "required|max_length[500]",
        "contact_number"            => "required|max_length[500]",
        "email_address"             => "permit_empty|valid_email|max_length[500]",
        "sss_no"                    => "required|max_length[500]",
        "tin_no"                    => "required|max_length[500]",
        "philhealth_no"             => "required|max_length[500]",
        "pag_ibig_no"               => "required|max_length[500]",
        "educational_attainment"    => "required|alpha_numeric_space|max_length[500]",
        "emergency_name"            => "required|alpha_numeric_space|max_length[500]",
        "emergency_contact_no"      => "required|max_length[500]",
        "emergency_address"         => "required|max_length[500]",
        "name_of_spouse"            => "required|alpha_numeric_space|max_length[500]",
        "spouse_contact_no"         => "required|max_length[500]",
        "no_of_children"            => "required|numeric|max_length[500]",
        "spouse_address"            => "required|max_length[500]",
        "position"                  => "required|alpha_numeric_space|max_length[500]",
        "employment_status"         => "required|alpha_numeric_space|max_length[500]",
        "date_hired"                => "required"
    ];
    protected $validationMessages   = [
        "employee_id"               => [
            "required" => "This field is required.",
            "alpha_numeric" => "Must be in alpha numeric characters.",
            "max_length" => "Max length is 20.",
            "is_unique" => "This ID is existing"
        ],        
        "firstname"                 => [ 
            "required" => "This field is required.",
            "alpha_numeric_space" => "This field must only contain alpha numeric spaces.",
            "max_length" => "Max length is 500."
        ],
        "lastname"                  => [ 
            "required" => "This field is required.",
            "alpha_numeric_space" => "This field must only contain alpha numeric spaces.",
            "max_length" => "Max length is 500."
        ],
        "gender"                    => [ 
            "required" => "This field is required."
        ],
        "civil_status"              => [ 
            "required" => "This field is required.",
            "alpha_numeric_space" => "This field must only contain alpha numeric spaces.",
            "max_length" => "Max length is 500."
        ],
        "date_of_birth"             => [ 
            "required" => "This field is required."
        ],
        "place_of_birth"            => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "postal_code"               => [ 
            "required" => "This field is required.",
            "numeric" => "Must be in numeric characters.",
            "max_length" => "Max length is 500."
        ],
        "language"                  => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "address_province"          => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "address_city"              => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "address_brgy"              => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "address_sub"               => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "contact_number"            => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "email_address"             => [ 
            "valid_email" => "Enter valid email.",
            "max_length" => "Max length is 500."
        ],
        "sss_no"                    => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "tin_no"                    => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "philhealth_no"             => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "pag_ibig_no"               => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "educational_attainment"    => [ 
            "required" => "This field is required.",
            "alpha_numeric_space" => "Must be in alpha numeric space characters.",
            "max_length" => "Max length is 500."
        ],
        "emergency_name"            => [ 
            "required" => "This field is required.",
            "alpha_numeric_space" => "Must be in alpha numeric space characters.",
            "max_length" => "Max length is 500."
        ],
        "emergency_contact_no"      => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "emergency_address"         => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "name_of_spouse"            => [ 
            "required" => "This field is required.",
            "alpha_numeric_space" => "Must be in alpha numeric space characters.",
            "max_length" => "Max length is 500."
        ],
        "spouse_contact_no"         => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "no_of_children"            => [ 
            "required" => "This field is required.",
            "numeric" => "Must be in numeric characters.",
            "max_length" => "Max length is 500."
        ],
        "spouse_address"            => [ 
            "required" => "This field is required.",
            "max_length" => "Max length is 500."
        ],
        "position"                  => [ 
            "required" => "This field is required.",
            "alpha_numeric_space" => "Must be in alpha numeric space characters.",
            "max_length" => "Max length is 500."
        ],
        "employment_status"         => [ 
            "required" => "This field is required.",
            "alpha_numeric_space" => "Must be in alpha numeric space characters.",
            "max_length" => "Max length is 500."
        ],
        "date_hired"                => [ 
            "required" => "This field is required."
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

    public function noticeTable() {
        $db      = \Config\Database::connect();
        $builder = $db->table('employees_view');
        $builder->select("*");
        return $builder;
    }

    public function buttonEdit(){
        $closureFun = function($row){
            return <<<EOF
                <a href="edit-customer/{$row['employee_id']}" class="btn btn-block btn-warning btn-xs" target="_blank"><i class="fas fa-edit"></i> Edit</a>
                <button class="btn btn-block btn-danger btn-xs delete-customer" data-toggle="modal" data-target="#modal-delete-customer" data-id="{$row['employee_id']}"><i class="fas fa-trash"></i> Delete</button>
            EOF; 
        };
        return $closureFun;
    }
}
