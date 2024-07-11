<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\CustomerSupportLogModel;

class CustomerSupportLog extends BaseController
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
     * @var array
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
        $this->_model           = new CustomerSupportLogModel(); // Current model
        $this->_module_code     = MODULE_CODES['customer_supports']; // Current module
        $this->_permissions     = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add         = $this->checkPermissions($this->_permissions, ACTION_ADD);
    }

    /**
     * Get list of records
     *
     * @return array|string
     */
    public function list()
    {
        
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
            'message'   => res_lang('success.added', 'Log')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $request        = $this->request->getVar();
                $id             = $request['id'];
                $inputs         = [
                    'customer_support_id'   => $id,
                    'findings'              => $request['findings'] ?? null,
                    'action'                => $request['action'] ?? null,
                    'troubleshooting'       => $request['troubleshooting'] ?? null,
                ];

                $this->checkRoleActionPermissions($this->_module_code, 'ADD_LOG', true);

                if (! $this->_model->save($inputs)) {
                    $errors             = $this->_model->errors();

                    foreach ($errors as $key => $value) {
                        $errors['logs_'. $key] = $value;
                    }

                    $data['errors']     = $errors;
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                }

                return $data;
            }
        );

        return $response;
    }

    /**
     * For getting the item data using the id
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved', 'Logs')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $data['data']   = $this->_model->fetch($id, 'DESC');

                return $data;
            },
            false
        );

        return $response;
    }
}