<?php

namespace App\Models;

use CodeIgniter\Model;
use GuzzleHttp\Promise\Is;

class AccountModel extends Model
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
        'employee_id',
        'username',
        'password',
        'access_level',
        'profile_img'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'employee_id'   => 'is_unique[accounts.employee_id]|required',
        'username'      => 'alpha_numeric|is_unique[accounts.username]|required|min_length[4]',
        'password'      => 'required',
        'access_level'  => 'required'
    ];
    protected $validationMessages   = [
        'employee_id' => [
            'is_unique' => 'This employee is already added in the accounts.',
            'required' => 'This field is required.'
        ],
        'username' => [
            'is_unique' => 'This username has already been taken! Please try a different one.',
            'alpha_numeric' => 'Only alpha numeric characters is allowed (A-Z, a-z, 0-9)'
        ],
        'password' => [
            'alpha_numeric' => 'Only alpha numeric characters is allowed (A-Z, a-z, 0-9)'
        ],
        'access_level' => [
            'required' => 'This field is required.'
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
    protected $beforeDelete   = ['checkRecordIfOneself'];
    protected $afterDelete    = [];

    // Check user trying to delete own account
    protected function checkRecordIfOneself($data) 
    {
        $id     = $data['id'];
        $result = $this->getAccounts($id, session('username'));

        if (! empty($result)) {
            throw new \Exception("You can't delete your own account!", 2);
        }
    }

    // Authenticate login user
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

    // Get profile image path
    public function getProfileImg($username) 
    {
        $account = $this->select('profile_img')->where('username', $username)->first();
        return $account['profile_img'];
    }

    // Get accounts
    public function getAccounts($id = null, $username = null) 
    {
        $builder = $this->select('account_id, employee_id, username, access_level, profile_img');
        $builder->where('deleted_at IS NULL');

        if ($id) {
            return $username 
                ? $builder->where('username', $username)->find($id)
                : $builder->find($id);
        }

        return $builder->findAll();
    }

    // For dataTables
    public function noticeTable() 
    {
        $builder = $this->db->table($this->view);
        $builder->select('id, employee_id, employee_name, username, access_level');

        if (session('access_level') !== AAL_ADMIN) {
            $builder->whereNotIn('UPPER(access_level)', [strtoupper(AAL_ADMIN)]);
        }
        
        return $builder;
    }

    public function dtAccessLevel() 
    {
        $access_level = function($row) {
            return get_roles($row['access_level']);
        };

        return $access_level;
    }

    // DataTable action buttons
    public function buttons($permissions)
    {
        $id         = 'id';
        $closureFun = function($row) use($id, $permissions) {
            $buttons = dt_button_actions($row, $id, $permissions);
            return $buttons;
        };
        
        return $closureFun;
    }
}
