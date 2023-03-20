<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Accounts as ModelsAccounts;

class AccountProfile extends BaseController
{
    /**
     * Use to initialize PermissionModel class
     * @var object
     */
    private $_model;

    /**
     * Default profile img path
     * @var string
     */
    private $_profileImgPath;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_model           = new ModelsAccounts(); // Current model
        $this->_profileImgPath  = '../public/uploads/profile';
    }

    /**
     * For displaying the view
     *
     * @return view
     */
    public function index()
    {
        $data['title']          = 'Account Profile';
        $data['page_title']     = 'Account Profile';
        $data['custom_js']      = 'accounts/profile.js';
        $data['sweetalert2']    = true;
        $data['account']        = $this->_getAccountDetails();
        $data['profile_img']    = $this->_getProfileImg(session('gender'));

        return view('accounts/profile', $data);
    }

    /**
     * Process for changing password
     *
     * @return json
     */
    public function changePassword()
    {
        $data = [];
        
        // Using DB Transaction
        $this->transBegin();

        try {            
            if (! $this->validate($this->_rules())) {
                $data['status']     = STATUS_ERROR;
                $data ['errors']    = $this->validator->getErrors();
                $data ['message']   = 'Validation error!';
            } else {
                $model          = new ModelsAccounts();
                $username       = session()->get('username');
                $curr_password  = $this->request->getVar('current_password');

                $data['status']     = STATUS_ERROR;
                $data['message']    = "Wrong current password! Please try again.";

                if ($account = $model->authenticate($username, $curr_password)) {
                    $new_password = $this->request->getVar('password');
                    $hash_password = password_hash(
                        $new_password, 
                        PASSWORD_DEFAULT
                    );
    
                    $data['status']     = STATUS_SUCCESS;
                    $data['message']    = "You have successfully changed your password! You will be logged out now in <b></b>...";

                    // Turn protection off - to skip validation
                    $model->protect(false);
                    
                    $model->where('username', $username)
                        ->set(['password' => $hash_password])
                        ->update();

                    // Turn protection on
                    $model->protect(true);

                    if (! empty($new_password)) {
                        // Send mail to employee
                        $res = $this->sendMail($this->request->getVar(), 'regular');
                        $msg = $res['message'];

                        if($res['status'] === STATUS_SUCCESS) {
                            $msg = $data['message'] . $msg;
                        }

                        $data['status'] = $res['status'];
                        $data['message'] = $msg;
                    }
                }
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception $e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            // $data['errors']     = $e->getMessage();
            $data ['message']   = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data); 
    }

    /**
     * For uploading profile image
     *
     * @return json
     */
    public function changeProfileImage()
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'You have successfully changed your profile image! Page will be reloaded in <b></b>...'
        ];
        
        // Using DB Transaction
        $this->transBegin();

        try {
            $validate = $this->validate([
                'profile_img' => [
                    'label' => 'Image file',
                    'rules' => [
                        'uploaded[profile_img]',
                        'ext_in[profile_img,jpg,jpeg,png]',
                        'max_size[profile_img,5120]',
                    ],
                    'errors' => [
                        'ext_in' => 'File must be image only (jpg,jpeg,png).',
                        'max_size' => 'Image file size must be 5mb only.',
                    ]
                ]
            ]);
        
            if (! $validate) {
                $data['status']     = STATUS_ERROR;
                $data ['errors']    = $this->validator->getErrors();
                $data ['message']   = 'Validation error!';
            } else {
                $img        = $this->request->getFile('profile_img');
                $username   = session('username');

                if ($img->isValid() && ! $img->hasMoved()) {
                    $newName = $username . '.' . $img->getClientExtension();

                    // Move file to new location
                    $img->move($this->_profileImgPath, $newName, true);
        
                    $model = new ModelsAccounts();
                    $model->where('username', $username)
                        ->set(['profile_img' => $newName])
                        ->update();
                }
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception $e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            // $data['errors']     = $e->getMessage();
            $data ['message']   = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data); 
    }

    /**
     * Rules for the required inputs
     *
     * @return array
     */
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

    /**
     * Get account details
     *
     * @return array
     */
    private function _getAccountDetails()
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

        $db = $this->builder;
        $query = $db->table($table)->select($fields)
                    ->where('employee_id', session()->get('employee_id'))
                    ->get();

        $account = $query->getRowArray();    

        return $account;
    }

    /**
     * Get profile img
     *
     * @return string
     */
    private function _getProfileImg($gender = null)
    {
        $profile_img = $this->_model->getProfileImg(session('username'));
        $profile_img_res = base_url('uploads/profile/' . $profile_img);
        
        if (empty($profile_img)) {
            $profile_img_res = base_url(get_avatar(strtolower($gender ?? 'male')));
        }

        return $profile_img_res;
    }
}
