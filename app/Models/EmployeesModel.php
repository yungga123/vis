<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeesModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'employees';
    protected $view             = 'employees_view';
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
        "firstname"                 => "required|alpha_space|min_length[2]|max_length[100]",
        "lastname"                  => "required|alpha_space|min_length[2]|max_length[100]",
        "middlename"                => "permit_empty|min_length[2]|max_length[100]",
        "gender"                    => "required",
        "civil_status"              => "required",
        "date_of_birth"             => "required",
        "place_of_birth"            => "required|max_length[255]",
        "postal_code"               => "required|numeric",
        "language"                  => "required|max_length[100]",
        "address_province"          => "required|max_length[150]",
        "address_city"              => "required|max_length[150]",
        "address_brgy"              => "required|max_length[150]",
        "address_sub"               => "required|max_length[150]",
        "contact_number"            => "required|numeric",
        "email_address"             => "required|valid_email",
        "sss_no"                    => "required|max_length[20]",
        "tin_no"                    => "required|max_length[20]",
        "philhealth_no"             => "required|max_length[20]",
        "pag_ibig_no"               => "required|max_length[20]",
        "educational_attainment"    => "required|alpha_numeric_space|max_length[255]",
        "emergency_name"            => "required|alpha_numeric_space|max_length[200]",
        "emergency_contact_no"      => "required|max_length[150]",
        "emergency_address"         => "required|max_length[255]",
        "name_of_spouse"            => "required_with[civil_status.married]|permit_empty|max_length[100]",
        "spouse_contact_no"         => "required_with[civil_status.married]|permit_empty|max_length[50]",
        "no_of_children"            => "required_with[civil_status.married]|permit_empty|numeric|max_length[2]",
        "spouse_address"            => "required_with[civil_status.married]|max_length[255]",
        "position"                  => "required|alpha_numeric_space|max_length[100]",
        "employment_status"         => "required",
        "date_hired"                => "required"
    ];
    protected $validationMessages   = [
        "employee_id"               => [
            "label" => "Employee ID",
            "required" => "Employee ID is required.",
            "alpha_numeric" => "Employee ID must be in alpha numeric characters.",
            "is_unique" => "Employee ID has already been taken! Please try a different one."
        ],        
        "firstname" => [ 
            "label" => "First Name",
            "required" => "First Name is required.",
            "alpha_numeric_space" => "First Name must only contain alpha numeric spaces.",
        ],
        "lastname" => [ 
            "label" => "Last Name",
            "required" => "Last Name is required.",
            "alpha_numeric_space" => "Last Name must only contain alpha numeric spaces.",
        ],
        "gender" => [ 
            "required" => "Gender is required."
        ],
        "civil_status" => [ 
            "label" => "Civil Status",
            "required" => "Civil Status is required.",
        ],
        "date_of_birth" => [ 
            "label" => "Date of Birth",
            "required" => "Date of Birth is required."
        ],
        "place_of_birth" => [ 
            "label" => "Place of Birth",
            "required" => "Place of Birth is required.",
        ],
        "postal_code" => [ 
            "label" => "Postal Code",
            "required" => "Postal Code is required.",
        ],
        "language" => [ 
            "label" => "Language",
            "required" => "Language is required.",
        ],
        "address_province" => [ 
            "label" => "Province",
            "required" => "Province is required.",
        ],
        "address_city" => [ 
            "label" => "City/Municipality",
            "required" => "City/Municipality is required.",
        ],
        "address_brgy" => [ 
            "label" => "Barangay",
            "required" => "Barangay is required.",
        ],
        "address_sub" => [ 
            "label" => "Additional Address",
            "required" => "Additional Address is required.",
        ],
        "contact_number" => [ 
            "label" => "Contact Number",
            "required" => "Contact Number is required.",
        ],
        "email_address" => [ 
            "label" => "Email Address",
            "required" => "Email Address is required."
        ],
        "sss_no" => [ 
            "label" => "SSS Number",
            "required" => "SSS Number is required.",
        ],
        "tin_no" => [ 
            "label" => "TIN Number",
            "required" => "TIN Number is required.",
        ],
        "philhealth_no" => [ 
            "label" => "PHILHEALTH Number",
            "required" => "PHILHEALTH Number is required.",
        ],
        "pag_ibig_no" => [ 
            "label" => "PAGIBIG Number",
            "required" => "PAGIBIG Number is required.",
        ],
        "educational_attainment" => [ 
            "label" => "Educational Attainment",
            "required" => "Educational Attainment is required.",
        ],
        "emergency_name" => [ 
            "label" => "Emergency Name",
            "required" => "Emergency Name is required.",
        ],
        "emergency_contact_no" => [ 
            "label" => "Emergency Contact Number",
            "required" => "Emergency Contact Number is required.",
        ],
        "emergency_address" => [ 
            "label" => "Emergency Address",
            "required" => "Emergency Address is required.",
        ],
        "name_of_spouse" => [ 
            "label" => "Name of Spouse",
            "required" => "Name of Spouse is required.",
        ],
        "spouse_contact_no" => [ 
            "label" => "Spouse Contact Number",
            "required" => "Spouse Contact Number is required.",
        ],
        "no_of_children" => [ 
            "label" => "Number of Children",
            "required" => "Number of children is required.",
        ],
        "spouse_address" => [ 
            "label" => "Spouse Address",
            "required" => "Spouse Address is required.",
        ],
        "position" => [ 
            "label" => "Position",
            "required" => "Position is required.",
        ],
        "employment_status" => [ 
            "label" => "Employment Status",
            "required" => "Employment Status is required.",
        ],
        "date_hired"  => [ 
            "label" => "Date Hired",
            "required" => "Date Hired is required."
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

    public function getEmployeeDetails($employee_id, $columns = '*')
    {
        $builder = $this->db->table($this->view);
        return $builder->select($columns)
                    ->where('employee_id', $employee_id)
                    ->get()->getRowArray();
    }

    // For dataTables
    public function noticeTable() 
    {
        $builder = $this->db->table($this->view);
        $builder->select("*");

        return $builder;
    }

    public function buttons($permissions)
    {
        $id = $this->primaryKey;
        $closureFun = function($row) use($id, $permissions) {
            if (is_admin()) {
                return <<<EOF
                    <button class="btn btn-sm btn-warning" onclick="edit({$row["$id"]})"  data-toggle="modal" data-target="#account_modal" title="Edit"><i class="fas fa-edit"></i> </button> 
                    <button class="btn btn-sm btn-danger" onclick="remove({$row["$id"]})" title="Delete"><i class="fas fa-trash"></i></button>  
                EOF;
            }
            
            $disabled = '';
            if (session('employee_id') === $row["employee_id"]) {
                $disabled = 'disabled';
            }

            $edit = '<button class="btn btn-sm btn-warning" title="Cannot edit" disabled><i class="fas fa-edit"></i> </button>';

            if (check_permissions($permissions, 'EDIT') && !is_admin()) {
                $edit = <<<EOF
                    <button class="btn btn-sm btn-warning" onclick="edit({$row["$id"]})"  data-toggle="modal" data-target="#account_modal" title="Edit"><i class="fas fa-edit"></i> </button> 
                EOF;
            }

            $delete = '<button class="btn btn-sm btn-danger" title="Cannot delete" disabled><i class="fas fa-trash"></i> </button>';

            if (check_permissions($permissions, 'DELETE') && !is_admin()) {
                $delete = <<<EOF
                    <button class="btn btn-sm btn-danger" onclick="remove({$row["$id"]})" title="Delete" $disabled><i class="fas fa-trash"></i></button>  
                EOF;
            }

            return $edit. $delete;
        };
        return $closureFun;
    }
}
