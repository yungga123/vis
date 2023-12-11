<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;
use App\Services\Mail\HRMailService;

class EmployeeModel extends Model
{
    /* Declare trait here to use */
    use HRTrait;

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
        'employee_id',
        'firstname',
        'middlename',
        'lastname',
        'gender',
        'civil_status',
        'date_of_birth',
        'place_of_birth',
        'postal_code',
        'address_province',
        'address_city',
        'address_brgy',
        'address_sub',
        'contact_number',
        'email_address',
        'sss_no',
        'tin_no',
        'philhealth_no',
        'pag_ibig_no',
        'educational_attainment',
        'course',
        'emergency_name',
        'emergency_contact_no',
        'emergency_address',
        'name_of_spouse',
        'spouse_contact_no',
        'no_of_children',
        'spouse_address',
        'position',
        'employment_status',
        'date_hired',
        'date_resigned'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'employee_id'               => 'required|alpha_numeric|max_length[20]|is_unique[employees.employee_id]',
        'firstname'                 => 'required|alpha_space|min_length[2]|max_length[100]',
        'lastname'                  => 'required|alpha_space|min_length[2]|max_length[100]',
        'middlename'                => 'permit_empty|min_length[2]|max_length[100]',
        'gender'                    => 'required',
        'civil_status'              => 'required',
        'date_of_birth'             => 'required',
        'place_of_birth'            => 'required|max_length[255]',
        'postal_code'               => 'required|numeric',
        'address_province'          => 'required|max_length[150]',
        'address_city'              => 'required|max_length[150]',
        'address_brgy'              => 'required|max_length[150]',
        'address_sub'               => 'required|max_length[150]',
        'contact_number'            => 'required|numeric',
        'email_address'             => 'required|valid_email',
        'sss_no'                    => 'required|max_length[20]',
        'tin_no'                    => 'required|max_length[20]',
        'philhealth_no'             => 'required|max_length[20]',
        'pag_ibig_no'               => 'required|max_length[20]',
        'educational_attainment'    => 'required|alpha_numeric_space|max_length[255]',
        'emergency_name'            => 'required|alpha_numeric_space|max_length[200]',
        'emergency_contact_no'      => 'required|max_length[150]',
        'emergency_address'         => 'required|max_length[255]',
        'name_of_spouse'            => 'required_with[civil_status.married]|permit_empty|max_length[100]',
        'spouse_contact_no'         => 'required_with[civil_status.married]|permit_empty|max_length[50]',
        'no_of_children'            => 'required_with[civil_status.married]|permit_empty|numeric|max_length[2]',
        'spouse_address'            => 'required_with[civil_status.married]|max_length[255]',
        'position'                  => 'required|alpha_numeric_space|max_length[100]',
        'employment_status'         => 'required',
        'date_hired'                => 'required'
    ];
    protected $validationMessages   = [
        'employee_id'               => [
            'label' => 'Employee ID',
            'required' => 'Employee ID is required.',
            'alpha_numeric' => 'Employee ID must be in alpha numeric characters.',
            'is_unique' => 'Employee ID has already been taken! Please try a different one.'
        ],        
        'firstname' => [ 
            'label' => 'First Name',
            'required' => 'First Name is required.',
            'alpha_numeric_space' => 'First Name must only contain alpha numeric spaces.',
        ],
        'lastname' => [ 
            'label' => 'Last Name',
            'required' => 'Last Name is required.',
            'alpha_numeric_space' => 'Last Name must only contain alpha numeric spaces.',
        ],
        'gender' => [ 
            'required' => 'Gender is required.'
        ],
        'civil_status' => [ 
            'label' => 'Civil Status',
            'required' => 'Civil Status is required.',
        ],
        'date_of_birth' => [ 
            'label' => 'Date of Birth',
            'required' => 'Date of Birth is required.'
        ],
        'place_of_birth' => [ 
            'label' => 'Place of Birth',
            'required' => 'Place of Birth is required.',
        ],
        'postal_code' => [ 
            'label' => 'Postal Code',
            'required' => 'Postal Code is required.',
        ],
        'address_province' => [ 
            'label' => 'Province',
            'required' => 'Province is required.',
        ],
        'address_city' => [ 
            'label' => 'City/Municipality',
            'required' => 'City/Municipality is required.',
        ],
        'address_brgy' => [ 
            'label' => 'Barangay',
            'required' => 'Barangay is required.',
        ],
        'address_sub' => [ 
            'label' => 'Additional Address',
            'required' => 'Additional Address is required.',
        ],
        'contact_number' => [ 
            'label' => 'Contact Number',
            'required' => 'Contact Number is required.',
        ],
        'email_address' => [ 
            'label' => 'Email Address',
            'required' => 'Email Address is required.'
        ],
        'sss_no' => [ 
            'label' => 'SSS Number',
            'required' => 'SSS Number is required.',
        ],
        'tin_no' => [ 
            'label' => 'TIN Number',
            'required' => 'TIN Number is required.',
        ],
        'philhealth_no' => [ 
            'label' => 'PHILHEALTH Number',
            'required' => 'PHILHEALTH Number is required.',
        ],
        'pag_ibig_no' => [ 
            'label' => 'PAGIBIG Number',
            'required' => 'PAGIBIG Number is required.',
        ],
        'educational_attainment' => [ 
            'label' => 'Educational Attainment',
            'required' => 'Educational Attainment is required.',
        ],
        'emergency_name' => [ 
            'label' => 'Emergency Name',
            'required' => 'Emergency Name is required.',
        ],
        'emergency_contact_no' => [ 
            'label' => 'Emergency Contact Number',
            'required' => 'Emergency Contact Number is required.',
        ],
        'emergency_address' => [ 
            'label' => 'Emergency Address',
            'required' => 'Emergency Address is required.',
        ],
        'name_of_spouse' => [ 
            'label' => 'Name of Spouse',
            'required' => 'Name of Spouse is required.',
        ],
        'spouse_contact_no' => [ 
            'label' => 'Spouse Contact Number',
            'required' => 'Spouse Contact Number is required.',
        ],
        'no_of_children' => [ 
            'label' => 'Number of Children',
            'required' => 'Number of children is required.',
        ],
        'spouse_address' => [ 
            'label' => 'Spouse Address',
            'required' => 'Spouse Address is required.',
        ],
        'position' => [ 
            'label' => 'Position',
            'required' => 'Position is required.',
        ],
        'employment_status' => [ 
            'label' => 'Employment Status',
            'required' => 'Employment Status is required.',
        ],
        'date_hired'  => [ 
            'label' => 'Date Hired',
            'required' => 'Date Hired is required.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedBy'];
    protected $afterInsert    = ['mailNotif'];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = ['deleteEmployeeAccounts'];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = ['checkRecordIfOneself'];
    protected $afterDelete    = ['deleteEmployeeAccounts'];

    /* Custom variables */
    protected $resigned       = 'Resigned';

    // For DataTable columns
    protected $dtColumns      = [
        'employee_id',
        'employee_name', 
        'address', 
        'gender', 
        'civil_status', 
        'date_of_birth', 
        'place_of_birth', 
        'position', 
        'employment_status', 
        'date_hired', 
        'date_resigned', 
        'contact_number', 
        'email_address', 
        'sss_no', 
        'tin_no', 
        'philhealth_no', 
        'pag_ibig_no', 
        'educational_attainment', 
        'course', 
        'emergency_name', 
        'emergency_contact_no', 
        'emergency_address', 
        'name_of_spouse', 
        'spouse_contact_no', 
        'no_of_children', 
        'spouse_address',
        'created_by',
        'created_at'
    ];

    // Set the value for created_by before inserting
    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = session('username');
        return $data;
    }

    // Mail notif after record created
    protected function mailNotif(array $data)
    {
        $id = $data['id'];

        if ($data['result']) {
            $columns    = "
                {$this->table}.employee_id,
                CONCAT({$this->table}.firstname, ' ', {$this->table}.lastname) AS employee_name,
                {$this->table}.gender,
                {$this->table}.civil_status,
                {$this->table}.position,
                {$this->table}.date_hired,
                {$this->table}.employment_status,
                {$this->table}.email_address,
                {$this->table}.contact_number,
                {$this->table}.created_at,
                IF({$this->table}.created_at = '' OR {$this->table}.created_at IS NULL, 'N/A', cb.employee_name) AS created_by
            ";
            $builder    = $this->select($columns);
            $builder->where("{$this->table}.id", $id);
            $this->joinAccountView($builder, "{$this->table}.created_by", 'cb');
            $record     = $builder->first();

            // Send mail notification
            $service = new HRMailService();
            $service->sendEmployeeMailNotif($record);
        }
        
        return $data;
    }

    // If employee record is deleted or marked as resigned
    // soft delete their corresponding account(s)
    protected function deleteEmployeeAccounts(array $data)
    {
        log_msg($data);
        if ($data['result']) {
            $id     = $data['id'];
            $status = $data['data']['employment_status'] ?? null;

            if ($status || isset($data['purge'])) {
                $record = $this->select('employee_id')->find($data['id']);
                log_msg($record);

                // Delete corresponding accounts - if exist
                // $accountModel = new AccountModel();
                // $accountModel->where('employee_id', $record[0]['employee_id'])->delete();
            }
        }

        return $data;
    }


    // Filter for not including resigned employees
    public function withOutResigned($builder = null) 
    {
        ($builder ?? $this)
            ->where("date_resigned = '' OR date_resigned IS NULL")
            ->where('employment_status !=', $this->resigned);
            
        return $this;
    }

    // Check user trying to delete own record
    public function checkRecordIfOneself(array $data) 
    {
        $id     = $data['id'];
        $result = $this->getEmployees($id, null, 'employee_id');

        if ($result[0]['employee_id'] === session('employee_id'))  {
            throw new \Exception("You can't delete your own record!", 2);
        }
    }

    // Get employees
    public function getEmployees($id = null, $employee_id = null, $columns = null, $without_resign = false) 
    {
        $builder = $this->select($columns ?? $this->allowedFields);
        $builder->where('deleted_at IS NULL');

		// Whether to not include resigned employees
		// Default - resigned are included
		if ($without_resign) $this->withOutResigned($builder);

        if ($id) {            
            return (! is_array($id))
                ? $builder->where('id', $id)->first()
                : $builder->find($id);
        }

        if ($employee_id) {
            return is_array($employee_id)
                ? $builder->whereIn('employee_id', $employee_id)->find()
                : $builder->where('employee_id', $employee_id)->first();
        }

        return $builder->findAll();
    }

    // Get employee row details
    public function getEmployeeInView($employee_id, $columns = null)
    {
        $builder = $this->db->table($this->view);
        $builder->select($columns ?? $this->allowedFields);
        $builder->where('employee_id', $employee_id);

        return $builder->get()->getRowArray();
    }

    // For dataTables
    public function noticeTable() 
    {
        $builder = $this->db->table($this->view);

        if (! is_admin()) {
            $builder->where('employee_id !=', DEVELOPER_ACCOUNT);
        }

        return $builder;
    }

    // DataTable action buttons
    public function buttons($permissions)
    {
        $id         = $this->primaryKey;
        $closureFun = function($row) use($id, $permissions) {
            $buttons = dt_button_actions($row, $id, $permissions);

            if (check_permissions($permissions, 'CHANGE')) {
                // Change Employment Status
                $onclick = <<<EOF
                    onclick="change({$row[$id]}, '{$row['employee_id']}', '{$row['employment_status']}')" title="Change employment status"
                EOF;
                $buttons .= dt_button_html([
                    'text'      => '',
                    'button'    => 'btn-success',
                    'icon'      => 'fas fa-random',
                    'condition' => $onclick,
                ]);
            }

            return $buttons;
        };
        
        return $closureFun;
    }
}
