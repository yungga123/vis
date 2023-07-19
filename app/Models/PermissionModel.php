<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'permissions';
    protected $primaryKey       = 'permission_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'role_code',
        'module_code',
        'permissions',
        'added_by',
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
            'rules' => 'required',
            'label' => 'role code',
        ],
        'module_code'   => [
            'rules' => 'required',
            'label' => 'module code',
        ],
        'permissions'   => 'required',
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
    
    // Check role and module if exist
    public function checkRoleAndModule($role, $module) 
    {
        $builder = $this->db->table($this->table);
        $builder->where('role_code', $role);
        $builder->where('module_code', $module);
        $builder->where('deleted_at IS NULL');    

        return $builder->get()->getRowArray();
    }
    
    // Get the permissions for the current logged user
    public function getCurrUserPermissions() 
    {        
        return session('access_level') !== null 
                ? $this->select('role_code, module_code, permissions')
                    ->where('role_code', strtoupper(session('access_level')))
                    ->findAll()
                : [];
    }
    
    // Get the specific permissions for the current logged user
    public function getCurrUserSpecificPermissions($module_code) 
    {        
        return session('access_level') !== null 
                ? $this->select('role_code, module_code, permissions')
                    ->where('role_code', strtoupper(session('access_level')))
                    ->where('module_code', $module_code)
                    ->first()
                : [];
    }

    // For dataTables
    public function noticeTable() 
    {
        $builder = $this->db->table($this->table);    
        $builder->where('deleted_at IS NULL');

        return $builder;
    }

    public function dtCustomizeData()
    {
        $id = $this->primaryKey;

        $permission = function($row) {
            $permissions = explode(',', $row['permissions']);
            $span =  '';
            $bg = 'rounded-pill text-white pl-2 pr-2 pt-1 pb-1';

            foreach ($permissions as $val) {
                $action = get_actions($val, true);
                if ($val == 'VIEW') {
                    $span .= '<span class="bg-info '. $bg .'">'. $action .'</span>';
                } elseif ($val == 'ADD') {
                    $span .= '<span class="bg-primary '. $bg .'">'. $action .'</span>';
                } elseif ($val == 'EDIT') {
                    $span .= '<span class="bg-warning '. $bg .'">'. $action .'</span>';
                } elseif ($val == 'DELETE') {
                    $span .= '<span class="bg-danger '. $bg .'">'. $action .'</span>';
                } else {
                    $span .= '<span class="bg-secondary '. $bg .'">'. $action .'</span>';
                }
            }

            return $span;
        };

        $module = function($row) {
            return MODULES[$row['module_code']];
        };

        $role = function($row) {
            return get_roles($row['role_code']);
        };
        
        return compact('permission', 'module', 'role');
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

            $edit = '<button class="btn btn-sm btn-warning" title="Cannot edit" disabled><i class="fas fa-edit"></i> </button>';

            if (check_permissions($permissions, 'EDIT') && !is_admin()) {
                $edit = <<<EOF
                    <button class="btn btn-sm btn-warning" onclick="edit({$row["$id"]})"  data-toggle="modal" data-target="#account_modal" title="Edit"><i class="fas fa-edit"></i> </button> 
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
