<?php

namespace App\Controllers\Reports;

use App\Controllers\BaseController;
use App\Models\InventoryDropdownModel;
use App\Services\Export\ClientExportService;
use App\Services\Export\HRExportService;
use App\Services\Export\AdminExportService;
use App\Services\Export\InventoryExportService;
use App\Services\Export\PayrollExportService;
use App\Services\Export\SalesExportService;
use App\Services\Export\PurchasingExportService;
use App\Services\Export\FinanceExportService;

class ExportData extends BaseController
{
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
     * Use to get current permissions
     * @var bool
     */
    private $_can_generate;
    
    /**
     * Use to get all permissions
     * @var array
     */
    private $_perms;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_module_code     = MODULE_CODES['export_data']; // Current module
        $this->_permissions     = $this->getSpecificPermissions($this->_module_code);
        $this->_can_generate    = $this->checkPermissions($this->_permissions, 'GENERATE');
    }

    /**
     * Display the view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code, ACTION_VIEW);

        $module_name            = get_modules($this->_module_code);
        $data['title']          = $module_name;
        $data['page_title']     = $module_name;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = ['reports/export/index.js'];
        $data['modules']        = $this->_get_modules();
        $data['can_generate']   = $this->_can_generate;
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
        $this->_get_modules();

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

                $request['permissions'] = $this->_perms[$module] ?? [];
                
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
        $modules        = [];
        // Add here the no need exporting modules
        $excludes       = [
            'DASHBOARD',
            'MANAGER_OF_SALES',
            'MANAGER_OF_SALES_INDV',
            'SETTINGS_GENERAL_INFO',
            'SETTINGS_MAILCONFIG',
            'SETTINGS_PERMISSIONS',
            'SETTINGS_ROLES',
            'EXPORT_DATA',
            'PAYROLL_COMPUTATION',
            'PAYROLL_SETTINGS',
        ];
        $permissions    = format_results($this->permissions, 'module_code', 'permissions');

        // Modules to be displayed will be based
        // on if user has an access to that said module
        if (! empty($this->modules)) {
            foreach ($this->modules as $module) {
                if (! in_array($module, $excludes)) {
                    $_perms = isset($permissions[$module]) ? $permissions[$module] : get_generic_modules_actions($module);
                    $_perms = is_array($_perms) ? $_perms : explode(',', $_perms);

                    // Check if actions VIEW OR VIEW_ALL are in the $_perms
                    $value  = array_intersect([ACTION_VIEW, ACTION_VIEW_ALL], $_perms);
    
                    // If user is not an admin and has no VIEW permission
                    // for this module, then continue to the next loop
                    if (! is_admin() && empty($value)) {
                        continue;
                    }
    
                    $this->_perms[$module] = $_perms;

                    $module_name        = get_modules($module);
                    $modules[$module]   = $module_name;

                    if ($module === 'CUSTOMERS') {
                        // Include the customer branches
                        $modules['CUSTOMER_BRANCHES'] = 'Client Branches';
                    }

                    if ($module === 'INVENTORY') {
                        // Include inventory logs
                        $modules['INVENTORY_LOGS'] = 'Inventory Logs';
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
        $options = [
            'EMPLOYEES'             => [
                'type'      => 'multiple',
                'name'      => 'Employment Status',
                'options'   => get_employment_status(),
            ],
            'CUSTOMERS'             => [
                'type'      => 'multiple',
                'name'      => 'Client Type',
                'options'   => get_client_types(),
            ],
            'TASK_LEAD'             => [
                'type'      => 'multiple',
                'name'      => 'Status',
                'options'   => get_tasklead_status(),
            ],
            'TASK_LEAD_BOOKED'      => [
                'type'      => 'multiple',
                'name'      => 'Quarter',
                'options'   => get_quarters(),
            ],
            'INVENTORY'             => [
                'type'      => 'multiple',
                'name'      => 'Category',
                'options'   => $dropdowns,
            ],
            'ADMIN_JOB_ORDER'       => [
                'type'      => 'multiple',
                'name'      => 'Status',
                'options'   => get_jo_status('', true),
            ],
            'ADMIN_SCHEDULES'       => [
                'type'      => 'multiple',
                'name'      => 'Status',
                'options'   => get_schedule_type('', true),
            ],
            'INVENTORY_LOGS'        => [
                'type'      => 'multiple',
                'name'      => 'Logs Type',
                'options'   => [
                    'ITEM_IN'   => 'Item In',
                    'ITEM_OUT'  => 'Item Out',
                ],
            ],
            'INVENTORY_PRF'         => [
                'type'      => 'multiple',
                'name'      => 'Status',
                'options'   => $prf_options,
            ],
            'INVENTORY_PRF_ITEMS'   => [
                'type'      => 'multiple',
                'name'      => 'Status',
                'options'   => $prf_options,
            ],
            'PURCHASING_PO'         => [
                'type'      => 'multiple',
                'name'      => 'Status',
                'options'   => $po_options,
            ],
            'PURCHASING_PO_ITEMS'   => [
                'type'      => 'multiple',
                'name'      => 'Status',
                'options'   => $po_options,
            ],
            'PURCHASING_SUPPLIERS'  => [
                'type'      => 'multiple',
                'name'      => 'Supplier Type',
                'options'   => $supplier_options,
            ],
            'SUPPLIER_BRANCHES'     => [
                'type'      => 'multiple',
                'name'      => 'Supplier Type',
                'options'   => $supplier_options,
            ],
            'PURCHASING_RPF'        => [
                'type'      => 'multiple',
                'name'      => 'Status',
                'options'   => $rpf_options,
            ],
            'PURCHASING_RPF_ITEMS'  => [
                'type'      => 'multiple',
                'name'      => 'Status',
                'options'   => $rpf_options,
            ],
            'PAYROLL_LEAVE'         => [
                'type'      => 'single',
                'name'      => 'Status',
                'options'   => get_leave_status('', true),
            ],
            'PAYROLL_OVERTIME'      => [
                'type'      => 'single',
                'name'      => 'Status',
                'options'   => get_leave_status('', true),
            ],
            'PAYROLL_SALARY_RATES'  => [
                'type'      => 'multiple',
                'name'      => 'Rate Type',
                'options'   => get_salary_rate_type(),
            ],
            'FINANCE_BILLING_INVOICE'  => [
                'type'      => 'multiple',
                'name'      => 'Billing Status',
                'options'   => get_billing_status(),
            ],
        ];

        if (is_admin() || in_array(ACTION_VIEW_ALL, ($this->_perms['PAYROLL_LEAVE'] ?? []))) {
            $options['PAYROLL_LEAVE'] = [
                'type'      => 'single',
                'name'      => 'Options',
                'options'   => [
                    'all'   => 'All Leave',
                    'mine'  => 'My Leave',
                ],
            ];
        }

        if (is_admin() || in_array(ACTION_VIEW_ALL, ($this->_perms['PAYROLL_OVERTIME'] ?? []))) {
            $options['PAYROLL_OVERTIME'] = [
                'type'      => 'single',
                'name'      => 'Options',
                'options'   => [
                    'all'   => 'All Overtime',
                    'mine'  => 'My Overtime',
                ],
            ];
        }

        if (is_admin() || in_array(ACTION_VIEW_ALL, ($this->_perms['PAYROLL_PAYSLIP'] ?? []))) {
            $options['PAYROLL_PAYSLIP'] = [
                'type'      => 'single',
                'name'      => 'Options',
                'options'   => [
                    'all'   => 'All Payslip',
                    'mine'  => 'My Payslip',
                ],
            ];
        }

        if (is_admin() || in_array(ACTION_VIEW_ALL, ($this->_perms['PAYROLL_TIMESHEETS'] ?? []))) {
            $options['PAYROLL_TIMESHEETS'] = [
                'type'      => 'single',
                'name'      => 'Options',
                'options'   => [
                    'all'   => 'All Timesheets',
                    'mine'  => 'My Timesheets',
                ],
            ];
        }

        return $options;
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
            'INVENTORY_LOGS'        => [new InventoryExportService(), 'itemLogs'],
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
            'PAYROLL_LEAVE'         => [new PayrollExportService(), 'leave'],
            'PAYROLL_OVERTIME'      => [new PayrollExportService(), 'overtime'],
            'PAYROLL_PAYSLIP'       => [new PayrollExportService(), 'payslip'],
            'PAYROLL_TIMESHEETS'    => [new PayrollExportService(), 'timesheets'],
            'PAYROLL_SALARY_RATES'  => [new PayrollExportService(), 'salaryRates'],
            'FINANCE_BILLING_INVOICE'  => [new FinanceExportService(), 'billingInvoices'],
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
