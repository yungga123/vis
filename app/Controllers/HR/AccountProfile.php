<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Traits\AccountMailTrait;

class AccountProfile extends BaseController
{
    use AccountMailTrait;
    
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
        $this->_model           = new AccountModel(); // Current model
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
        $data['custom_js']      = 'account/profile.js';
        $data['sweetalert2']    = true;
        $data['account']        = $this->_getAccountDetails();
        $data['profile_img']    = $this->getProfileImg(session('gender'));

        return view('account/profile', $data);
    }

    /**
     * Process for changing password
     *
     * @return json
     */
    public function changePassword()
    {
        $data       = [
            'status'    => STATUS_ERROR,
            'message'   => 'Wrong current password! Please try again.'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                if (! $this->validate($this->_rules())) {
                    $data['status']     = STATUS_ERROR;
                    $data ['errors']    = $this->validator->getErrors();
                    $data ['message']   = 'Validation error!';
                } else {
                    $model          = new AccountModel();
                    $username       = session()->get('username');
                    $curr_password  = $this->request->getVar('current_password');
    
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Wrong current password! Please try again.";
    
                    if ($model->authenticate($username, $curr_password)) {
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
                            $employee_id        = $this->request->getVar('employee_id');
                            $mailMsg            = $this->sendMailAccountNotif($employee_id, $this->request->getVar(), true);
                            $data['message']    = $data['message'] . $mailMsg;
                        }
                    }
                }

                return $data;
            }
        );

        return $response; 
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
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $rules = [
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
                ];
            
                if (! $this->validate($rules)) {
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
            
                        $model = new AccountModel();
                        $model->where('username', $username)
                            ->set(['profile_img' => $newName])
                            ->update();
                    }
                }

                return $data;
            }
        );

        return $response;
    }

    /**
     * Get profile img
     *
     * @return string
     */
    public function getProfileImg($gender = null)
    {
        $profile_img = $this->_model->getProfileImg(session('username'));
        $profile_img_res = base_url('uploads/profile/' . $profile_img);
        
        if (empty($profile_img)) {
            $profile_img_res = base_url(get_avatar(strtolower($gender ?? 'male')));
        }

        return $profile_img_res;
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
}