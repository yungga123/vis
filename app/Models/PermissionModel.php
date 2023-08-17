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
            $permissions    = explode(',', $row['permissions']);
            $span           =  '';

            foreach ($permissions as $val) {
                $action = get_actions($val, true);
                $color  = dt_status_color($val);
                $span  .= text_badge($color, $action);
            }

            return $span;
        };

        $module = function($row) {
            return get_modules($row['module_code']);
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
            $buttons = dt_button_actions($row, $id, $permissions, false);
            return $buttons;
        };
        
        return $closureFun;
    }
}
