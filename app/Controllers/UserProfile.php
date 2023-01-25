<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Accounts as ModelsAccounts;

class UserProfile extends BaseController
{
    public function index()
    {
        $data['title'] = 'User Profile';
        $data['page_title'] = 'User Profile';
        $data['user'] = $this->_get_user_details();

        // print_r(session()->get());
        // return;
        
        return view('accounts/user/profile', $data);
    }

    public function change_password()
    {
        $data = [];
        
        try {            
            if (! $this->validate($this->_rules())) {
                $data['status']     = self::STATUS_ERROR;
                $data ['errors']    = $this->validator->getErrors();
                $data ['message']   = 'Validation error!';
            } else {
                $model          = new ModelsAccounts();
                $username       = session()->get('username');
                $curr_password  = $this->request->getVar('current_password');

                $data['status']     = self::STATUS_ERROR;
                $data['message']    = "Wrong current password! Please try again.";

                if ($user = $model->authenticate($username, $curr_password)) {
                    $hash_password = password_hash(
                        $this->request->getVar('password'), 
                        PASSWORD_DEFAULT
                    );

                    // $inputs = [
                    //     'employee_id'   => session()->get('employee_id'),
                    //     'username'      => $username,
                    //     'access_level'  => session()->get('access_level'),
                    //     'password'      => $hash_password,
                    // ];
    
                    $data['status']     = self::STATUS_SUCCESS;
                    $data['message']    = "You have successfully changed you password! You will be logged out now...";

                    // # Using this method of update due restriction in model
                    $builder = $this->qbuilder;
                    $builder->table($model->table)->where('username', $username)
                                ->set(['password' => $hash_password])
                                ->update();
                }
            }
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = self::STATUS_ERROR;
            // $data['errors']     = $e->getMessage();
            $data ['message']   = 'Error while processing data! Please contact you system administrator.';
        }

        return $this->response->setJSON($data); 
    }

    private function _rules()
    {
        $rules = [
            'current_password' => [
                'label' => 'current password',
                'rules' => 'required|min_length[8]|max_length[20]'
            ],
            'password' => [
                'label' => 'new password',
                'rules' => 'required|alpha_numeric|min_length[8]|max_length[20]|differs[current_password]'
                // 'rules' => 'required|alpha_numeric_punct|min_length[8]|max_length[20]'
            ],
            'confirm_password' => [
                'label' => 'confirm password',
                'rules' => 'required_with[password]|matches[password]'
            ]
        ];

        return $rules;
    }

    private function _get_user_details()
    {
        $table = 'employees_view';
        $fields = '
            employee_name,
            position,
            gender,
            civil_status,
            date_of_birth,
            address,
            contact_number,
            email_address
        ';

        $db = $this->qbuilder;
        $query = $db->table($table)->select($fields)
                    ->where('employee_id', session()->get('employee_id'))
                    ->get();

        $user = $query->getRowArray();
        $user['avatar'] = get_avatar(strtolower($user['gender']));

        return $user;
    }
}
