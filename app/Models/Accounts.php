<?php

namespace App\Models;

use CodeIgniter\Model;

class Accounts extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'accounts';
    protected $view             = 'accounts_view';
    protected $primaryKey       = 'account_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "employee_id",
        "username",
        "password",
        "access_level",
        "profile_img"
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        "employee_id"   => "is_unique[accounts.employee_id]|required",
        "username"      => "alpha_numeric|is_unique[accounts.username]|required|min_length[4]",
        "password"      => "required",
        "access_level"  => "required"
    ];
    protected $validationMessages   = [
        "employee_id" => [
            "is_unique" => "This employee is already added in the accounts.",
            "required" => "This field is required."
        ],
        "username" => [
            "is_unique" => "This username has already been taken! Please try a different one.",
            "alpha_numeric" => "Only alpha numeric characters is allowed (A-Z, a-z, 0-9)"
        ],
        "password" => [
            "alpha_numeric" => "Only alpha numeric characters is allowed (A-Z, a-z, 0-9)"
        ],
        "access_level" => [
            "required" => "This field is required."
        ],
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

    public function authenticate($username, $password) 
    {
        $user = $this->where('username', $username)->first();

        if (! empty($user)) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        
        return false;
    }

    public function getProfileImg($username) 
    {
        $account = $this->select('profile_img')
                    ->where('username', $username)
                    ->first();

        return $account['profile_img'];
    }

    public function findUser($user) 
    {
        return $this->find($user);
    }

    public function findUsername($user) 
    {
        return $this->where('username',$user)
                    ->findAll();
    }

    // For dataTables
    public function noticeTable() 
    {
        $builder = $this->db->table($this->view);

        if (session('access_level') !== AAL_ADMIN) {
            $builder->whereNotIn('UPPER(access_level)', [strtoupper(AAL_ADMIN)]);
        }
        
        return $builder;
    }

    public function dtAccessLevel() 
    {
        $access_level = function($row) use($old) {
            return get_roles($row['access_level']);
        };

        return $access_level;
    }

    public function buttons($permissions)
    {
        $id = 'id';
        $closureFun = function($row) use($id, $permissions) {
            if (is_admin()) {
                return <<<EOF
                    <button class="btn btn-sm btn-warning" onclick="edit({$row["$id"]})"  data-toggle="modal" data-target="#account_modal" title="Edit"><i class="fas fa-edit"></i> </button> 
                    <button class="btn btn-sm btn-danger" onclick="remove({$row["$id"]})" title="Delete"><i class="fas fa-trash"></i></button>  
                EOF;
            } else {
                $edit = '<button class="btn btn-sm btn-warning" title="Cannot edit" disabled><i class="fas fa-edit"></i> </button>';
                $delete = '<button class="btn btn-sm btn-danger" title="Cannot delete" disabled><i class="fas fa-trash"></i> </button>';

                if (check_permissions($permissions, 'EDIT')) {
                    $edit = <<<EOF
                        <button class="btn btn-sm btn-warning" onclick="edit({$row["$id"]})"  data-toggle="modal" data-target="#account_modal" title="Edit"><i class="fas fa-edit"></i> </button> 
                    EOF;
                }

                if (check_permissions($permissions, 'DELETE') && $row['username'] != session('username')) {
                    $delete = <<<EOF
                        <button class="btn btn-sm btn-danger" onclick="remove({$row["$id"]})" title="Delete"><i class="fas fa-trash"></i></button>  
                    EOF;
                }
            }

            return $edit. $delete;
        };
        return $closureFun;
    }
}
