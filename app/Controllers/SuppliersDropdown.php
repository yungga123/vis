<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SuppliersDropdownModel;

class SuppliersDropdown extends BaseController
{
    /**
     * Use to initialize PermissionModel class
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
        $this->_model       = new SuppliersDropdownModel(); // Current model
        $this->_module_code = MODULE_CODES['suppliers']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
    }

    public function index()
    {
        $data['title']          = 'Suppliers Dropdown';
        $data['page_title']     = 'Suppliers Dropdown';
        $data['custom_js']      = 'suppliers_dropdown/index.js';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['select2']        = true;
        //$data['can_add']        = $this->_can_add;
        //$data['btn_add_lbl']    = 'Add New Supplier';


        return view('suppliers_dropdown/index', $data);
    }
}
