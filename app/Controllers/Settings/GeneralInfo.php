<?php

namespace App\Controllers\Settings;

use App\Controllers\BaseController;
use App\Models\GeneralInfoModel;
use CodeIgniter\Database\RawSql;

class GeneralInfo extends BaseController
{
    /**
     * Use to initialize model class
     * @var object
     */
    private $_model;

    /**
     * Use to get current module code
     * @var string
     */
    private $_module_code;
    
    /**
     * Use to get current permissions
     * @var string
     */

    private $_permissions;

    /**
     * Use to check if can add
     * @var bool
     */
    private $_can_add;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_model       = new GeneralInfoModel(); // Current model
        $this->_module_code = MODULE_CODES['general_info']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
    }

    /**
     * Display the permission view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code);
        
        $data['title']          = 'Settings | General Info';
        $data['page_title']     = 'Settings | General Info';
        $data['custom_js']      = 'settings/general_info.js';
        $data['sweetalert2']    = true;
        $data['routes']         = json_encode([
            'general_info' => [
                'fetch' => url_to('general_info.fetch'),
            ],
        ]);

        return view('settings/general_info/index', $data);
    }

    /**
     * For saving data
     *
     * @return json
     */
    public function save() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Data has been saved successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->_canUserSave();

                $inputs     = [];
                $request    = $this->request->getVar();
                unset($request['csrf_test_name']);

                foreach ($request as $key => $value) {
                    $inputs[] = [
                        'key'   => $key,
                        'value' => $value,
                        'updated_by' => session('username'),
                    ];
                }
                
                $this->_model->singleSave($inputs);

                return $data;
            },
            true
        );

        return $response;
    }

    /**
     * For uploading files
     *
     * @return json
     */
    public function upload() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Data has been uploaded successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->_canUserSave();

                $fileName = 'company_logo';
                $validate = $this->_validationRule($fileName);

                if (! $this->validate($validate)) {
                    $data['status']     = STATUS_ERROR;
                    $data ['errors']    = $this->validator->getErrors();
                    $data ['message']   = 'Validation error!';

                    return $data;
                } 
                
                $img = $this->request->getFile($fileName);

                if ($img->isValid() && ! $img->hasMoved()) {
                    $newName    = 'company-logo.' . $img->getClientExtension();

                    // Move uploaded files to public folder
                    $img->move('../public/uploads/logo/', $newName, true);

                    // File path to display preview
                    $filepath   = ('uploads/logo/'. $newName);
                    $inputs     = [
                        'key'   => $fileName,
                        'value' => $filepath,
                        'updated_by' => session('username'),
                    ];
                
                    // Save file path to database
                    $this->_model->singleSave($inputs);
                }

                return $data;
            },
            true
        );

        return $response;
    }

    /**
     * For getting the data
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Data has been retrieved!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $data['data']       = $this->_model->fetchAll();
                $data['base_url']   = base_url();
                return $data;
            }
        );

        return $response;
    }

    /**
     * For uploading files validation rule
     *
     * @return array
     */
    private function _validationRule($fileName)
    {
        $validate = [
            "{$fileName}" => [
                'label' => 'Image file',
                'rules' => [
                    "uploaded[{$fileName}]",
                    "ext_in[{$fileName},jpg,jpeg,png]",
                    "mime_in[{$fileName},image/jpg,image/jpeg,image/png]",
                    "max_size[{$fileName},5120]",
                ],
                'errors' => [
                    'ext_in' => 'File must be image only (jpg,jpeg,png).',
                    'mime_in' => 'File must be image only (jpg,jpeg,png).',
                    'max_size' => 'Image file size must be 5mb only.',
                ]
            ]
        ];

        return $validate;
    }

    /**
     * Check if user can save
     *
     * @return void|Exception
     */
    private function _canUserSave()
    {
        if (! $this->_can_add) {
            throw new \Exception("You don't have permission for saving data. Kindly add the <strong>ADD</strong> permission first!", 2);
        }
    }
}
