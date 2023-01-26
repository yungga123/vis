<?php

namespace App\Models;

use CodeIgniter\Model;

class Accounts extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'accounts';
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
        "access_level"
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
        "username"      => "alpha_numeric|is_unique",
        "password"      => "alpha_numeric",
        "access_level"  => "required"
    ];
    protected $validationMessages   = [
        "employee_id" => [
            "is_unique" => "This employee is already added in the accounts.",
            "required" => "This field is required."
        ],
        "username" => [
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

    public function findUser($user) 
    {
        return $this->find($user);
    }

    public function findUsername($user) 
    {
        return $this->where('username',$user)
                    ->findAll();
    }

    public function noticeTable() 
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('accounts_view');
        $builder->select("*");
        return $builder;
    }

    public function buttonEdit()
    {
        $closureFun = function($row){
            return <<<EOF
                <a href="edit-account/{$row['id']}" class="btn btn-block btn-warning btn-xs" target="_blank"><i class="fas fa-edit"></i> Edit</a>
                <button class="btn btn-block btn-danger btn-xs delete-account" data-toggle="modal" data-target="#modal-delete-account" data-id="{$row['id']}"><i class="fas fa-trash"></i> Delete</button>
            EOF; 
        };
        return $closureFun;
    }
}
