<?php

namespace App\Controllers\Settings;

use App\Controllers\BaseController;
use App\Models\GeneralInfoModel;
use App\Traits\GeneralInfoTrait;
use App\Traits\FileUploadTrait;

class GeneralInfo extends BaseController
{
    /* Declare trait here to use */
    use GeneralInfoTrait, FileUploadTrait;

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
        $data['sweetalert2']    = true;
        $data['dropzone']       = true;
        $data['custom_js']      = ['settings/general_info.js', 'dropzone.js'];
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.saved')
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.uploaded', 'Image')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->_canUserSave();

                $fileName   = 'company_logo';
                $allowed    = implode(',', $this->imgExtensions);
                $validate = $this->validationFileRules($fileName, $allowed);

                if (! $this->validate($validate)) {
                    $data ['errors']    = $this->validator->getErrors();
                    $data['status']     = res_lang('status.error');
                    $data ['message']   = res_lang('error.validation');
                    return $data;
                } 
                
                $img            = $this->request->getFile($fileName);
                $newName        = $img->getRandomName();
                $downloadUrl    = base_url($this->initialFilePathLogo . $newName);
                // Upload image and get formatted file info
                $file           = $this->uploadFile($fileName, $img, $newName, $this->fullFilePathLogo(), $downloadUrl);

                // The data to save or update
                $inputs     = [
                    'key'   => $fileName,
                    'value' => $newName,
                    'updated_by' => session('username'),
                ];

                // Get the current record/ filename
                $curFilename = $this->getGeneralInfo($fileName);
            
                // Save or update file path to database
                if (! $this->_model->upsert($inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');

                    return $data;
                }

                if (! empty($curFilename)) {
                    // Remove the previous file
                    $filepath = $this->fullFilePathLogo() . $curFilename;
                    $this->removeFile($filepath);
                }
                
                $data['files'] = $file;
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                if ($q = $this->request->getVar('q')) {
                    $files              = [];
                    $filename           = $this->getGeneralInfo($q);

                    if (! empty($filename)) {
                        $nfilename          = strpos($filename, '/') ? explode('/', $filename) : $filename;
                        $nfilename          = is_array($nfilename) ? array_pop($nfilename) : $nfilename;
                        $downloadUrl        = base_url($this->initialFilePathLogo . $nfilename);
                        $files              = $this->getFiles($q, [$nfilename], $this->fullFilePathLogo(), $downloadUrl);
                    }

                    $data['files']      = $files;
                } else {
                    $data['data']       = $this->_model->fetchAll();
                    $data['base_url']   = base_url();
                }
                return $data;
            }
        );

        return $response;
    }

    /**
     * Check if user can save
     *
     * @return void|Exception
     */
    private function _canUserSave()
    {
        if (! $this->_can_add) {
            throw new \Exception(res_lang('restrict.permission.change'), 2);
        }
    }
}
