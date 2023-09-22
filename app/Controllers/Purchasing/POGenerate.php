<?php

namespace App\Controllers\Purchasing;

use App\Controllers\BaseController;
use monken\TablesIgniter;

class POGenerate extends BaseController
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
        //$this->_model       = new SupplierBrandsModel(); // Current model
        $this->_module_code = MODULE_CODES['generate_PO']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
    }

    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code);

        $data['title']          = get_modules($this->_module_code);
        $data['page_title']     = get_modules($this->_module_code);
        $data['can_add']        = $this->_can_add;
        $data['btn_add_lbl']    = 'Add Purchase Order';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = 'purchasing/generate_po/index.js';
        $data['router']         = json_encode(
            [
                'generate_po' => [
                    'list' => url_to('generate_po.list')
                ]
            ]
        );

        return view('purchasing/generate_po/index', $data);
    }

    public function list()
    {
        $table      = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request);

        $table->setTable($builder)
            ->setSearch([
            ])
            ->setOrder([
            ])
            ->setOutput([
            ]);

        return $table->getDatatable();
    }
    
}
