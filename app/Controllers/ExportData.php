<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InventoryDropdownModel;
use App\Models\EmployeeModel;
use App\Models\AccountModel;
use App\Models\CustomerModel;
use App\Models\CustomerBranchModel;
use App\Models\TaskLeadView;
use App\Models\InventoryModel;
use App\Models\ProjectRequestFormModel;
use App\Models\SuppliersModel;
use App\Models\SupplierBrandsModel;
use App\Models\PurchaseOrderModel;
use App\Models\RequestPurchaseFormModel;
use App\Models\JobOrderModel;
use App\Models\ScheduleModel;
use App\Models\DispatchModel;
use App\Services\Export\ClientExport;

class ExportData extends BaseController
{
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
     * Class constructor
     */
    public function __construct()
    {
        $this->_module_code = MODULE_CODES['export_data']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
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

        $module_name            = get_modules($this->_module_code);
        $data['title']          = $module_name;
        $data['page_title']     = $module_name;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = ['export/index.js'];
        $data['modules']        = $this->_get_modules();
        $data['php_to_js']      = $this->_get_modules_options();

        return view('export/index', $data);
    }

    /**
     * For exporting data to csv
     *
     * @return void
     */
    public function export() 
    {
        $rules = $this->_validationRules();

        if (! $this->validate($rules)) {
            // The validation fails, so returns the form.
            // return $this->validator->getErrors();
            return redirect()->back()->withInput();
        }

        // Gets the validated data.
        $post = $this->validator->getValidated();
        log_message('info', '$post => '. json_encode($post));

        $module = $this->request->getVar('module');
        // $this->_process($module, $post);

        return redirect()->back()->with('success', 'Data has been exported!');
    }
    
    /**
     * Get user modules with data to export
     * 
     * @return array
     */
    private function _get_modules()
    {
        $modules    = [];
        $excludes   = [
            'DASHBOARD',
            'MANAGER_OF_SALES',
            'MANAGER_OF_SALES_INDV',
            'SETTINGS_GENERAL_INFO',
            'SETTINGS_MAILCONFIG',
            'EXPORT_DATA'
        ];

        if (! empty($this->modules)) {
            foreach ($this->modules as $module) {
                if (! in_array($module, $excludes)) {
                    $modules[$module] = get_modules($module);

                    if ($module === 'CUSTOMERS') {
                        // Include the customer branches
                        $modules['CUSTOMER_BRANCHES'] = 'Customer Branches';
                    }

                    if ($module === 'TASK_LEAD') {
                        // Include the booked tasklead
                        $modules[$module . '_BOOKED'] = 'Task/Lead Booked';
                    }

                    if ($module === 'PURCHASING_SUPPLIERS') {
                        // Include the supplier branches
                        $modules['SUPPLIER_BRANCHES'] = 'Supplier Branches';
                    }
                }
            }
        }

        return $modules;
    }
    
    /**
     * Get modules options
     * 
     * @return array
     */
    private function _get_modules_options()
    {
        $invDropdownModel   = new InventoryDropdownModel();
        $dropdowns          = $invDropdownModel->getDropdowns('CATEGORY', 'dropdown, dropdown_type');
        $dropdowns          = flatten_array($dropdowns);

        return [
            'EMPLOYEES'             => [
                'name'      => 'Employment Status',
                'options'   => get_employment_status(),
            ],
            'CUSTOMERS'             => [
                'name'      => 'Client Type',
                'options'   => get_client_types(),
            ],
            'TASK_LEAD'             => [
                'name'      => 'Status',
                'options'   => get_tasklead_status(),
            ],
            'INVENTORY'             => [
                'name'      => 'Category',
                'options'   => $dropdowns,
            ],
            'PURCHASING_SUPPLIERS'  => [
                'name'      => 'Status',
                'options'   => get_supplier_type(),
            ],
            'ADMIN_JOB_ORDER'       => [
                'name'      => 'Status',
                'options'   => get_jo_status('', true),
            ],
            'ADMIN_SCHEDULES'       => [
                'name'      => 'Status',
                'options'   => get_schedule_type('', true),
            ],
            'INVENTORY_PRF'         => [
                'name'      => 'Status',
                'options'   => get_prf_status('', true),
            ],
            'PURCHASING_RPF'        => [
                'name'      => 'Status',
                'options'   => get_rpf_status('', true),
            ],
            'PURCHASING_PO'         => [
                'name'      => 'Status',
                'options'   => get_po_status('', true),
            ],
        ];
    }
    
    /**
     * Get module's model
     * 
     * @param string $module
     * @return array
     */
    private function _process($module, $filters)
    {
        // $models = [
        //     'ACCOUNTS'              => new AccountModel,
        //     'EMPLOYEES'             => new EmployeeModel,
        //     'CUSTOMERS'             => new CustomerModel,
        //     'CUSTOMER_BRANCHES'     => new CustomerBranchModel,
        //     'TASK_LEAD'             => new TaskLeadView,
        //     'TASK_LEAD_BOOKED'      => new TaskLeadView,
        //     'INVENTORY'             => new InventoryModel,
        //     'PURCHASING_SUPPLIERS'  => new SuppliersModel,
        //     'SUPPLIER_BRANCHES'     => new SupplierBrandsModel,
        //     'ADMIN_JOB_ORDER'       => new JobOrderModel,
        //     'ADMIN_SCHEDULES'       => new ScheduleModel,
        //     'ADMIN_DISPATCH'        => new DispatchModel,
        //     'INVENTORY_PRF'         => new ProjectRequestFormModel,
        //     'PURCHASING_RPF'        => new RequestPurchaseFormModel,
        //     'PURCHASING_PO'         => new PurchaseOrderModel,
        // ];

        $services = [
            // 'ACCOUNTS'              => new AccountModel,
            // 'EMPLOYEES'             => new EmployeeModel,
            'CUSTOMERS'             => (new ClientExport())->clients($filters),
            // 'CUSTOMER_BRANCHES'     => new CustomerBranchModel,
            // 'TASK_LEAD'             => new TaskLeadView,
            // 'TASK_LEAD_BOOKED'      => new TaskLeadView,
            // 'INVENTORY'             => new InventoryModel,
            // 'PURCHASING_SUPPLIERS'  => new SuppliersModel,
            // 'SUPPLIER_BRANCHES'     => new SupplierBrandsModel,
            // 'ADMIN_JOB_ORDER'       => new JobOrderModel,
            // 'ADMIN_SCHEDULES'       => new ScheduleModel,
            // 'ADMIN_DISPATCH'        => new DispatchModel,
            // 'INVENTORY_PRF'         => new ProjectRequestFormModel,
            // 'PURCHASING_RPF'        => new RequestPurchaseFormModel,
            // 'PURCHASING_PO'         => new PurchaseOrderModel,
        ];

        return $services[$module];
    }
    
    /**
     * Validation rules
     * 
     * @return array
     */
    private function _validationRules()
    {
        return [
            'module' => [
                'rules' => 'required',
                'label' => 'module'
            ],
            'start_date' => [
                'rules' => 'required',
                'label' => 'start date'
            ],
            'end_date' => [
                'rules' => 'required',
                'label' => 'end date'
            ],
        ];
    }
}
