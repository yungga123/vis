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
    protected $useSoftDeletes   = true;
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
    protected $beforeInsert   = ['setCreatedByValue'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = ['checkRecordIfOneself'];
    protected $afterDelete    = [];

    // Set the value for created_by before inserting
    protected function setCreatedByValue(array $data)
    {
        $data['data']['created_by'] = session('employee_id');
        return $data;
    }

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

    /**
     * Get accounts via view - either by employee_id or username
     */
    public function getAccountsView($id = null, $username = null, $columns = '') 
    {
        $columns = $columns ? $columns : '
            id, 
            employee_id, 
            employee_name, 
            username, 
            access_level, 
            profile_img,
            created_by_name,
            created_at
        ';
        $builder = $this->db->table($this->view)->select($columns);

        if ($username) {
            if (
                is_array($username) ||
                (is_string($username) && strpos(',', $username) !== false)
            ) {
                $username = is_array($username) ? $username : explode(',', $username);
                $builder->whereIn('username', clean_param($username));
            } else
                $builder->where('username', $username);
        }

        if ($id) {
            if (is_array($id)) {
                $builder->whereIn('employee_id', $id);
                return $builder->get()->getResultArray();
            }
           
            if (strpos(',', $id) !== false) {
                $builder->whereIn('employee_id', clean_param(explode(',', $id)));
                return $builder->get()->getResultArray();
            } 
            
            return $builder->where('employee_id', $id)->get()->getRowArray();

        } else {
            if (is_string($username) && strpos(',', $username) === false)
                return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    // For dataTables
    public function noticeTable() 
    {
        $builder = $this->db->table($this->view);
        $builder->select('id, employee_id, employee_name, username, access_level, created_by_name, created_at');

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
