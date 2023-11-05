<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerBranchModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'customer_branches';
    protected $accountsView     = 'accounts_view';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'customer_id',
        'branch_name',
        'province',
        'city',
        'barangay',
        'subdivision',
        'contact_person',
        'contact_number',
        'email_address',
        'notes',
        'referred_by',
        'created_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [        
        'branch_name' => [
            'rules' => 'required|string|min_length[2]|max_length[255]',
            'label' => 'branch name'
        ],
        'province' => [
            'rules' => 'required|string|min_length[2]|max_length[255]',
            'label' => 'province'
        ],
        'city' => [
            'rules' => 'required|string|min_length[2]|max_length[255]',
            'label' => 'city'
        ],
        'barangay' => [
            'rules' => 'permit_empty|string|min_length[2]|max_length[255]',
            'label' => 'barangay'
        ],
        'subdivision' => [
            'rules' => 'permit_empty|string|min_length[2]|max_length[255]',
            'label' => 'subdivision'
        ],
        'contact_person' => [
            'rules' => 'required|string|min_length[2]|max_length[255]',
            'label' => 'contact person'
        ],
        'contact_number' => [
            'rules' => 'required|string|min_length[6]|max_length[255]',
            'label' => 'contact number'
        ],
        'email_address' => [
            'rules' => 'permit_empty|string|min_length[2]|max_length[255]',
            'label' => 'email address'
        ],
        'notes' => [
            'rules' => 'permit_empty|string|min_length[5]|max_length[255]',
            'label' => 'notes'
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedByValue'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Set the value for created_by before inserting
    protected function setCreatedByValue(array $data)
    {
        $data['data']['created_by'] = session('username');
        return $data;
    }

    // Set the columns
    public function columns($dtTable = false)
    {
        $columns = "
            {$this->table}.id,
            {$this->table}.customer_id,
            {$this->table}.branch_name,
            province,
            city,
            barangay,
            subdivision,
            contact_person,
            contact_number,
            email_address,
            notes
        ";

        if ($dtTable) {
            $datetimeFormat = dt_sql_datetime_format();
            $addressConcat  = dt_sql_concat_client_address();
            $columns        .= ",
                {$addressConcat},
                DATE_FORMAT(created_at, '{$datetimeFormat}') AS created_at,
                {$this->accountsView}.employee_name AS created_by
            ";
        }

        return $columns;
    }

    // For dataTables
    public function noticeTable($customer_id) 
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->columns(true));
        $builder->join($this->accountsView, "{$this->table}.created_by = {$this->accountsView}.username", 'left');
        $builder->where('customer_id', $customer_id);
        $builder->where("deleted_at IS NULL");
        $builder->orderBy('id', 'DESC');

        return $builder;
    }

    // DataTable action buttons
    public function buttons($permissions)
    {
        $id         = $this->primaryKey;
        $closureFun = function($row) use($id, $permissions) {
            $buttons = '';
            if (check_permissions($permissions, 'EDIT')) {
                $buttons .= <<<EOF
                    <button class="btn btn-sm btn-warning" onclick="editBranch({$row["$id"]})" title="Edit Branch"><i class="fas fa-edit"></i> </button> 
                EOF;
            }

            if (check_permissions($permissions, 'DELETE')) {
                $buttons .= <<<EOF
                    <button class="btn btn-sm btn-danger" onclick="removeBranch({$row["$id"]})" title="Delete Branch"><i class="fas fa-trash"></i></button>  
                EOF;
            }

            return $buttons ? $buttons : '~~N/A~~';
        };
        
        return $closureFun;
    }
}
