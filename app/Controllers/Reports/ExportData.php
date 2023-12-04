<?php

namespace App\Controllers\Reports;

use App\Controllers\BaseController;
use App\Models\InventoryDropdownModel;
use App\Services\Export\ClientExportService;
use App\Services\Export\HRExportService;
use App\Services\Export\AdminExportService;
use App\Services\Export\InventoryExportService;
use App\Services\Export\SalesExportService;
use App\Services\Export\PurchasingExportService;

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
        $data['custom_js']      = ['reports/export/index.js'];
        $data['modules']        = $this->_get_modules();
        $data['php_to_js_options']  = json_encode($this->_get_modules_options());

        return view('reports/export/index', $data);
    }

    /**
     * For exporting data to csv
     *
     * @return void
     */
    public function export() 
    {
        try {
            $rules = $this->_validationRules();
    
            if (! $this->validate($rules)) {
                // If the validation fails, redirect back
                return redirect()->back()->withInput();
            }
    
            // Get request filters
            $request    = $this->request->getVar();
            $module     = $request['module'];
    
            // Get the specific export service
            $service = $this->_getExportService($module);

            if (is_array($service)) {
                $class  = $service[0];
                $method = $service[1];

                if ($module === 'TASK_LEAD_BOOKED') {
                    // Call the export function
                    $class->$method($request, true);
                }
                
                // Call the export function
                $class->$method($request);
            }
        } catch (\Exception $e) {
            $error = $e->getCode() != 0 ? $e->getMessage() : res_lang('error.process');
            log_message('error', 'Export Exception: {exception}', ['exception' => $e]);

            return redirect()->back()->withInput()->with('error', $error);
        }
    }
    
    /**
     * Get user modules with data to export
     * 
     * @return array
     */
    private function _get_modules()
    {
        $modules    = [];
        // Add here the no need exporting modules
        $excludes   = [
            'DASHBOARD',
            'MANAGER_OF_SALES',
            'MANAGER_OF_SALES_INDV',
            'SETTINGS_GENERAL_INFO',
            'SETTINGS_MAILCONFIG',
            'SETTINGS_PERMISSIONS',
            'SETTINGS_ROLES',
            'EXPORT_DATA'
        ];

        // Modules to be displayed will be based
        // on if user has an access to that said module
        if (! empty($this->modules)) {
            foreach ($this->modules as $module) {
                if (! in_array($module, $excludes)) {
                    $module_name        = get_modules($module);
                    $modules[$module]   = $module_name;

                    if ($module === 'CUSTOMERS') {
                        // Include the customer branches
                        $modules['CUSTOMER_BRANCHES'] = 'Client Branches';
                    }

                    if ($module === 'INVENTORY_PRF') {
                        // Include the prf items
                        $modules['INVENTORY_PRF_ITEMS'] = 'Project Request Items';
                    }

                    if ($module === 'TASK_LEAD') {
                        // Include the booked tasklead
                        $modules[$module . '_BOOKED'] = 'Task/Lead Booked';
                    }

                    if ($module === 'PURCHASING_SUPPLIERS') {
                        // Include the supplier branches
                        $modules['SUPPLIER_BRANCHES'] = 'Supplier Branches';
                    }

                    if ($module === 'PURCHASING_PO') {
                        // Include the purchase order items
                        $modules['PURCHASING_PO_ITEMS'] = 'Purchase Order Items';
                    }

                    if ($module === 'PURCHASING_RPF') {
                        // Include the rpf items
                        $modules['PURCHASING_RPF_ITEMS'] = 'Request to Purchase Items';
                    }
                }
            }
        }

        // Sort modules by value
        asort($modules);

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
        $dropdowns          = $invDropdownModel->getDropdowns('CATEGORY', 'dropdown_id AS id, dropdown AS text');
        $prf_options        = array_replace(get_prf_status('', true), ['item_out' => 'Item Out']);
        $po_options         = get_po_status('', true);
        $rpf_options        = get_rpf_status('', true);
        $supplier_options   = get_supplier_type();

        // Add here if module has filter like status
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
            'TASK_LEAD_BOOKED'      => [
                'name'      => 'Quarter',
                'options'   => get_quarters(),
            ],
            'INVENTORY'             => [
                'name'      => 'Category',
                'options'   => $dropdowns,
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
                'options'   => $prf_options,
            ],
            'INVENTORY_PRF_ITEMS'   => [
                'name'      => 'Status',
                'options'   => $prf_options,
            ],
            'PURCHASING_PO'         => [
                'name'      => 'Status',
                'options'   => $po_options,
            ],
            'PURCHASING_PO_ITEMS'   => [
                'name'      => 'Status',
                'options'   => $po_options,
            ],
            'PURCHASING_SUPPLIERS'  => [
                'name'      => 'Supplier Type',
                'options'   => $supplier_options,
            ],
            'SUPPLIER_BRANCHES'     => [
                'name'      => 'Supplier Type',
                'options'   => $supplier_options,
            ],
            'PURCHASING_RPF'        => [
                'name'      => 'Status',
                'options'   => $rpf_options,
            ],
            'PURCHASING_RPF_ITEMS'  => [
                'name'      => 'Status',
                'options'   => $rpf_options,
            ],
        ];
    }
    
    /**
     * Get the export service
     * 
     * @param string $module
     * @return array
     */
    private function _getExportService($module)
    {
        // If new main module, please create a new file
        // in the \App\Services\Export\ directory
        $services = [
            'ACCOUNTS'              => [new HRExportService(), 'accounts'],
            'EMPLOYEES'             => [new HRExportService(), 'employees'],
            'CUSTOMERS'             => [new ClientExportService(), 'clients'],
            'CUSTOMER_BRANCHES'     => [new ClientExportService(), 'branches'],
            'ADMIN_JOB_ORDER'       => [new AdminExportService(), 'jobOrders'],
            'ADMIN_SCHEDULES'       => [new AdminExportService(), 'schedules'],
            'ADMIN_DISPATCH'        => [new AdminExportService(), 'dispatch'],
            'INVENTORY'             => [new InventoryExportService(), 'items'],
            'INVENTORY_PRF'         => [new InventoryExportService(), 'prf'],
            'INVENTORY_PRF_ITEMS'   => [new InventoryExportService(), 'prfItems'],
            'TASK_LEAD'             => [new SalesExportService(), 'taskleads'],
            'TASK_LEAD_BOOKED'      => [new SalesExportService(), 'taskleads'],
            'PURCHASING_PO'         => [new PurchasingExportService(), 'purchaseOrders'],
            'PURCHASING_PO_ITEMS'   => [new PurchasingExportService(), 'poItems'],
            'PURCHASING_SUPPLIERS'  => [new PurchasingExportService(), 'suppliers'],
            'SUPPLIER_BRANCHES'     => [new PurchasingExportService(), 'supplierBranches'],
            'PURCHASING_RPF'        => [new PurchasingExportService(), 'rpf'],
            'PURCHASING_RPF_ITEMS'  => [new PurchasingExportService(), 'rpfItems'],
        ];

        // Return the initailized service class and the method name
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
