<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Models\EmployeeModel;
use App\Traits\AccountMailTrait;
use App\Traits\FileUploadTrait;

class AccountProfile extends BaseController
{
    /* Declare trait here to use */
    use AccountMailTrait, FileUploadTrait;
    
    /**
     * Use to initialize PermissionModel class
     * @var object
     */
    private $_model;

    /**
     * The root directory to save the uploaded files
     * @var array
     */
    private $_rootDirPath;

    /**
     * The initial file path after the root path
     * @var array
     */
    private $_initialFilePath;

    /**
     * The full file path
     * @var array
     */
    private $_fullFilePath;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_model           = new AccountModel(); // Current model
        // Change it desired directory path
        // Example F:\files
        $this->_rootDirPath     = FCPATH; // The public folder
        // Please don't change this
        $this->_initialFilePath = 'uploads/profile/';
        $this->_fullFilePath    = $this->_rootDirPath . $this->_initialFilePath;
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
        $data['custom_js']      = ['hr/account/profile.js', 'dropzone.js'];
        $data['sweetalert2']    = true;
        $data['dropzone']       = true;
        $data['account']        = $this->_getAccountDetails();
        $data['profile_img']    = $this->getProfileImg(session('gender'));

        return view('hr/account/profile', $data);
    }

    /**
     * Process for changing password
     *
     * @return json
     */
    public function changePassword()
    {
        $data       = [
            'status'    => res_lang('status.error'),
            'message'   => 'Wrong current password! Please try again.'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                if (! $this->validate($this->_rules())) {
                    $data['status']     = res_lang('status.error');
                    $data ['errors']    = $this->validator->getErrors();
                    $data ['message']   = res_lang('error.validation');
                } else {
                    $model          = new AccountModel();
                    $username       = session('username');
                    $curr_password  = $this->request->getVar('current_password');
    
                    $data['status']     = res_lang('status.error');
                    $data['message']    = "Wrong current password! Please try again.";
    
                    if ($model->authenticate($username, $curr_password)) {
                        $new_password = $this->request->getVar('password');
                        $hash_password = password_hash(
                            $new_password, 
                            PASSWORD_DEFAULT
                        );
        
                        $data['status']     = res_lang('status.success');
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
            'status'    => res_lang('status.success'),
            'message'   => 'You have successfully changed your profile image! Page will be reloaded in <b></b>...'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                // Name of the file
                $name       = 'profile_img';
                $allowed    = implode(',', $this->imgExtensions);
                $rules      = $this->validationFileRules($name, $allowed, 5, 'Profile Image');
            
                if (! $this->validate($rules)) {
                    $data['status']     = res_lang('status.error');
                    $data ['errors']    = $this->validator->getErrors();
                    $data ['message']   = res_lang('error.validation');

                    return $data;
                }
                
                $img            = $this->request->getFile('profile_img');
                $username       = session('username');
                $newName        = $username . '_' . $img->getRandomName();
                $downloadUrl    = base_url($this->_initialFilePath . $newName);
                // Upload image and get formatted file info
                $file           = $this->uploadFile($username, $img, $newName, $this->_fullFilePath, $downloadUrl);

                // Get the current record/ filename
                $curFilename = $this->_model->getProfileImg($username);
                
                // Save or update file path to database
                $save = $this->_model->where('username', $username)
                    ->set(['profile_img' => $newName])->update();

                if (! $save) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');

                    return $data;
                }

                // Remove the previous file
                $filepath = $this->_fullFilePath . $curFilename;
                $this->removeFile($filepath);
                
                $data['files'] = $file;
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
        $profile_img        = $this->_model->getProfileImg(session('username'));
        $profile_img_res    = base_url($this->_initialFilePath . $profile_img);
        
        if (empty($profile_img)) {
            $profile_img_res = base_url(get_avatar(strtolower($gender ?? 'male')));
        }

        return $profile_img_res;
    }

    /**
     * Get account details
     *
     * @return array
     */
    private function _getAccountDetails()
    {
        $model          = new EmployeeModel();
        $employee_id    = session('employee_id');
        $fields         = '
            employee_name,
            position,
            gender,
            civil_status,
            date_of_birth,
            address,
            contact_number,
            email_address
        ';
        $record         = $model->getEmployeeDetails($employee_id, $fields); 

        return $record;
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
            ],
            'confirm_password' => [
                'label' => 'confirm password',
                'rules' => 'required_with[password]|matches[password]'
            ]
        ];

        return $rules;
    }
}
