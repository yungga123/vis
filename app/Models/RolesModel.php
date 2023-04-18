<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'roles';
    protected $primaryKey       = 'role_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'role_code', 
        'description',
        'created_by',
        'updated_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'role_code'     => [
            'rules' => 'required|is_unique[roles.role_code]|min_length[2]|max_length[50]|alpha_dash',
            'label' => 'role code',
        ],
        'description'   => [
            'rules' => 'required|min_length[5]|max_length[150]|',
            'label' => 'description',
        ]
    ];
    protected $validationMessages   = [];
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

    // Check if role has already dependency in permissions or accounts
    public function getRoles() 
    {
        $data   = [];
        $roles  = $this->select('role_code, description')->findAll();

        if (! empty($roles)) {
            foreach ($roles as $role) {
                $data[$role['role_code']] = $role['description'];
            }
        }

        return $data;
    }

    // Check if role has already dependency in permissions or accounts
    public function getSpecificRoleCode($role_id) 
    {
        $role = $this->select('role_code')->first($role_id);

        return (! empty($role)) ? $role['role_code'] : null;
    }

    // Check if role has already dependency in permissions or accounts
    public function roleHasDependecy($role_code) 
    {
        $accounts       = $this->checkRoleDependencyInAccounts($role_code);
        $permissions    = $this->checkRoleDependencyInPermissions($role_code);

        return (empty($accounts) && empty($permissions)) ? false : true;
    }

    // Check role dependency in accounts
    public function checkRoleDependencyInAccounts($role_code)
    {
        $builder = $this->db->table('accounts')->select('access_level');
        $builder->where('UPPER(access_level)', $role_code);
        $builder->where('deleted_at IS NULL');

        return $builder->get()->getResultArray();
    }

    // Check role dependency in permissions
    public function checkRoleDependencyInPermissions($role_code)
    {
        $builder = $this->db->table('permissions');
        $builder->where('role_code', $role_code);
        $builder->where('deleted_at IS NULL');

        return $builder->get()->getResultArray();
    }

    // For dataTables
    public function noticeTable() 
    {
        $builder = $this->db->table($this->table);
        $builder->select('role_id, role_code, description');
        $builder->where('deleted_at IS NULL');

        return $builder;
    }

    public function buttons($permissions)
    {
        $id = $this->primaryKey;
        $closureFun = function($row) use($id, $permissions) {
            if (is_admin()) {
                return <<<EOF
                    <button class="btn btn-sm btn-warning" onclick="edit({$row["$id"]})"  title="Edit"><i class="fas fa-edit"></i> </button> 

                    <button class="btn btn-sm btn-danger" onclick="remove({$row["$id"]})" title="Delete"><i class="fas fa-trash"></i></button>  
                EOF;
            }

            $edit = '<button class="btn btn-sm btn-warning" title="Cannot edit" disabled><i class="fas fa-edit"></i> </button>';

            if (check_permissions($permissions, 'EDIT') && !is_admin()) {
                $edit = <<<EOF
                    <button class="btn btn-sm btn-warning" onclick="edit({$row["$id"]})"  title="Edit"><i class="fas fa-edit"></i> </button> 
                EOF;
            }

            $delete = '<button class="btn btn-sm btn-danger" title="Cannot delete" disabled><i class="fas fa-trash"></i> </button>';

            if (check_permissions($permissions, 'DELETE') && !is_admin()) {
                $delete = <<<EOF
                    <button class="btn btn-sm btn-danger" onclick="remove({$row["$id"]})" title="Delete"><i class="fas fa-trash"></i></button>  
                EOF;
            }

            return $edit. $delete;
        };
        
        return $closureFun;
    }
}
