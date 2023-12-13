<?php

namespace App\Controllers\Payroll;

use App\Controllers\BaseController;

class Computation extends BaseController
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
        // $this->_model           = new SalaryRateModel(); // Current model
        $this->_module_code     = MODULE_CODES['payroll_computation']; // Current module
        $this->_permissions     = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add         = $this->checkPermissions($this->_permissions, ACTION_ADD);
    }

    /**
     * Display the view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code);

        $data['title']          = 'Payroll Computation';
        $data['page_title']     = 'Payroll Computation';
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['moment']         = true;
        $data['custom_js']      = ['payroll/computation/index.js', 'moment.js'];
        $data['custom_css']     = 'payroll/computation/index.css';
        $data['routes']         = json_encode([
            'payroll'    => [
                'computation' => [
                    // 'list'      => url_to('payroll.computation.list'),
                    // 'fetch'     => url_to('payroll.computation.fetch'),
                    // 'delete'    => url_to('payroll.computation.delete'),
                ],
            ],
            'hr' => [
                'common'    => [
                    'employees' => url_to('hr.common.employees'),
                ],
            ],
        ]);

        return view('payroll/computation/index', $data);
    }
}
