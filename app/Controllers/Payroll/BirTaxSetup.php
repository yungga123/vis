<?php

namespace App\Controllers\Payroll;

use App\Controllers\BaseController;
use App\Models\BirTaxModel;

class BirTaxSetup extends BaseController
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
        $this->_model           = new BirTaxModel(); // Current model
        $this->_module_code     = MODULE_CODES['payroll_settings']; // Current module
        $this->_permissions     = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add         = $this->checkPermissions($this->_permissions, ACTION_ADD);
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
            'message'   => res_lang('success.added')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, ACTION_SAVE, true);

                $id         = $this->request->getVar('id');
                $request    = $this->request->getVar();
                $rb_type    = strtoupper($request['rb_type']) === 'AMOUNT' ? NULL : $request['rb_type'];
                $inputs     = [
                    'id'                        => $request['id'],
                    'compensation_range_start'  => floatval($request['compensation_range_start']),
                    'compensation_range_end'    => $rb_type ? 0 : floatval($request['compensation_range_end']),
                    'fixed_tax_amount'          => floatval($request['fixed_tax_amount']),
                    'compensation_level'        => floatval($request['compensation_level']),
                    'tax_rate'                  => clean_param($request['tax_rate'], 'floatval', ' %'),
                    'below_or_above'            => $rb_type,
                ];
    
                if ($id) {
                    $data['message']    = res_lang('success.updated');
                }

                if (!  $this->_model->save($inputs)) {
                    $data['errors']     = $this->_model->errors();
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
            'message'   => res_lang('success.retrieved')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $model  = $this->_model;
                $model->orderBy('fixed_tax_amount', 'ASC');

                $id     = $this->request->getVar('id') ?? null;
                $record = $id ? $model->fetch($id) : $model->fetchAll();

                $data['data']   = $record;
                return $data;
            },
            false
        );

        return $response;
    }

    /**
     * Deleting record
     *
     * @return json
     */
    public function delete() 
    {
        $data = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.deleted')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, ACTION_SAVE, true);

                $id = $this->request->getVar('id');

                if (! $this->_model->delete($id)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                }

                return $data;
            }
        );

        return $response;
    }
}
