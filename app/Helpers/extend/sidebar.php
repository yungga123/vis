<?php
if (! function_exists('get_user_modules'))
{
    /**
     * Get the modules of the current logged user
     */
	function get_user_modules(?array $permissions = null): array 
	{
        if (session('access_level') === AAL_ADMIN) {
            return array_keys(MODULES);
        }

        $generic        = get_generic_modules_actions();
        $permissions    = $permissions ?? get_permissions();

        if (empty($permissions)) return $generic;
        
        // If $permissions is not empty
        // get the module codes
        $modules = array_column($permissions, 'module_code');

        // Merge them with the generic
        $_modules = array_unique(array_merge($modules, $generic));

        return $_modules;
	}
}

if (! function_exists('get_nav_menus'))
{
    /**
     * Get the nav menus of some of the modules
     */
	function get_nav_menus(?string $param = null): array
	{
        $is_human_resource = (
            url_is('accounts') || 
            url_is('employees') || 
            url_is('payroll/salary-rates') || 
            url_is('payroll/computation') || 
            url_is('payroll/payslip') || 
            url_is('payroll/leave') ||
            url_is('payroll/settings') ||
            url_is('payroll/overtime') ||
            url_is('payroll/timesheets')
        );
        $is_sales       = (
            url_is('tasklead') || 
            url_is('tasklead/booked') || 
            url_is('sales_manager') || 
            url_is('sales_manager_indv')
        );
        $is_inventory = (
            url_is('inventory') || 
            url_is('inventory/dropdowns') || 
            url_is('inventory/logs') || 
            url_is('project-request-forms') || 
            url_is('inventory/order-forms')
        );
        $is_purchasing  = (
            url_is('suppliers') || 
            url_is('request-purchase-forms') ||
            url_is('purchase-orders')
        );
        $is_settings    = (
            url_is('settings/mail') || 
            url_is('settings/permissions') || 
            url_is('settings/roles') || 
            url_is('settings/general-info')
        );
        $is_finance     = (
            url_is('finance/billing-invoice') ||
            url_is('finance/funds')
        );
        
		$menu           = [
            'SALES'          => [
                'name'      => 'Sales',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => $is_sales,
                'icon'      => 'far fa-credit-card',
            ],
            'HUMAN_RESOURCE'    => [
                'name'      => 'Human Resource',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => $is_human_resource,
                'icon'      => 'fas fa-users',
            ],
            'SETTINGS'      => [
                'name'      => 'Settings',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => $is_settings,
                'icon'      => 'fas fa-cog',
            ],
            'PURCHASING'        => [
                'name'      => 'Purchasing',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => $is_purchasing,
                'icon'      => 'fas fa-money-check',
            ],
            'ADMIN'             => [
                'name'      => 'Admin',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => (url_is('job-orders') || url_is('schedules') || url_is('dispatch')),
                'icon'      => 'fas fa-users-cog',
            ],
            'INVENTORY'         => [
                'name'      => 'Inventory',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => $is_inventory,
                'icon'      => 'fas fa-store-alt',
            ],
            'REPORTS'         => [
                'name'      => 'Reports',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => url_is('reports/export'),
                'icon'      => 'fas fa-server',
            ],
            'FINANCE'         => [
                'name'      => 'Finance',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => $is_finance,
                'icon'      => 'fas fa-wallet',
            ],
        ];

        return $param ? $menu[$param] : $menu;
	}
}

if (! function_exists('setup_modules'))
{
    /**
     * Setup modules - like icons, urls etc..
     */
	function setup_modules(string $param = null)
	{
		$modules = [
            'ACCOUNTS'              => [
                'menu'      => 'HUMAN_RESOURCE', // Leave empty if none
                'name'      => get_modules('ACCOUNTS'),
                'url'       => url_to('account.home'),
                'class'     => (url_is('accounts') ? 'active' : ''),
                'icon'      => 'fas fa-user-cog',
            ],
            'EMPLOYEES'             => [
                'menu'      => 'HUMAN_RESOURCE', // Leave empty if none
                'name'      => get_modules('EMPLOYEES'),
                'url'       => url_to('employee.home'),
                'class'     => (url_is('employees') ? 'active' : ''),
                'icon'      => 'fas fa-user-clock',
            ],
            'CUSTOMERS'  => [
                'menu'      => '', // Leave empty if none
                'name'      => get_modules('CUSTOMERS'),
                'url'       => url_to('customer.home'),
                'class'     => (url_is('clients') ? 'active' : ''),
                'icon'      => 'far fa-address-card',
            ],
            'TASK_LEAD'             => [
                'menu'      => 'SALES', // Leave empty if none
                'name'      => get_modules('TASK_LEAD'),
                'url'       => url_to('tasklead.home'),
                'class'     => (url_is('tasklead') || url_is('tasklead/booked') ? 'active' : ''),
                'icon'      => 'fas fa-tasks',
            ],
            'MANAGER_OF_SALES'      => [
                'menu'      => 'SALES', // Leave empty if none
                'name'      => get_modules('MANAGER_OF_SALES'),
                'url'       => url_to('sales_manager.home'),
                'class'     => (url_is('sales_manager') ? 'active' : ''),
                'icon'      => 'fas fa-user-tie',
            ],
            'MANAGER_OF_SALES_INDV' => [
                'menu'      => 'SALES', // Leave empty if none
                'name'      => get_modules('MANAGER_OF_SALES_INDV'),
                'url'       => url_to('sales_manager_indv.home'),
                'class'     => (url_is('sales_manager_indv') ? 'active' : ''),
                'icon'      => 'fas fa-user-tag',
            ],
            'INVENTORY'             => [
                'menu'      => 'INVENTORY', // Leave empty if none
                'name'      => get_modules('INVENTORY'),
                'url'       => url_to('inventory.home'),
                'class'     => (url_is('inventory') || url_is('inventory/dropdowns') || url_is('inventory/logs') ? 'active' : ''),
                'icon'      => 'fas fa-shopping-cart',
            ],
            'SETTINGS_MAILCONFIG'   => [
                'menu'      => 'SETTINGS', // Leave empty if none
                'name'      => get_modules('SETTINGS_MAILCONFIG'),
                'url'       => url_to('mail_config.home'),
                'class'     => (url_is('settings/mail') ? 'active' : ''),
                'icon'      => 'fas fa-envelope',
            ],
            'SETTINGS_PERMISSIONS'  => [
                'menu'      => 'SETTINGS', // Leave empty if none
                'name'      => get_modules('SETTINGS_PERMISSIONS'),
                'url'       => url_to('permission.home'),
                'class'     => (url_is('settings/permissions') ? 'active' : ''),
                'icon'      => 'fas fa-user-lock',
            ],
            'SETTINGS_ROLES'        => [
                'menu'      => 'SETTINGS', // Leave empty if none
                'name'      => get_modules('SETTINGS_ROLES'),
                'url'       => url_to('roles.home'),
                'class'     => (url_is('settings/roles') ? 'active' : ''),
                'icon'      => 'fas fa-user-tag',
            ],
            'ADMIN_JOB_ORDER'       => [
                'menu'      => 'ADMIN', // Leave empty if none
                'name'      => get_modules('ADMIN_JOB_ORDER'),
                'url'       => url_to('job_order.home'),
                'class'     => (url_is('job-orders') ? 'active' : ''),
                'icon'      => 'fas fa-user-md',
            ],
            'ADMIN_SCHEDULES'       => [
                'menu'      => 'ADMIN', // Leave empty if none
                'name'      => get_modules('ADMIN_SCHEDULES'),
                'url'       => url_to('schedule.home'),
                'class'     => (url_is('schedules') ? 'active' : ''),
                'icon'      => 'fas fa-calendar-alt',
            ],
            'ADMIN_DISPATCH'        => [
                'menu'      => 'ADMIN', // Leave empty if none
                'name'      => get_modules('ADMIN_DISPATCH'),
                'url'       => url_to('dispatch.home'),
                'class'     => (url_is('dispatch') ? 'active' : ''),
                'icon'      => 'fas fa-user-astronaut',
            ],
            'INVENTORY_PRF'   => [
                'menu'      => 'INVENTORY', // Leave empty if none
                'name'      => get_modules('INVENTORY_PRF'),
                'url'       => url_to('prf.home'),
                'class'     => (url_is('project-request-forms') ? 'active' : ''),
                'icon'      => 'fas fa-sign-out-alt',
            ],
            'PURCHASING_SUPPLIERS'      => [
                'menu'      => 'PURCHASING', // Leave empty if none
                'name'      => get_modules('PURCHASING_SUPPLIERS'),
                'url'       => url_to('suppliers.home'),
                'class'     => (url_is('suppliers') ? 'active' : ''),
                'icon'      => 'fas fa-truck-loading',
            ],
            'PURCHASING_RPF'        => [
                'menu'      => 'PURCHASING', // Leave empty if none
                'name'      => get_modules('PURCHASING_RPF'),
                'url'       => url_to('rpf.home'),
                'class'     => (url_is('request-purchase-forms') ? 'active' : ''),
                'icon'      => 'fas fa-shopping-bag',
            ],
            'PURCHASING_PO'           => [
                'menu'      => 'PURCHASING', // Leave empty if none
                'name'      => get_modules('PURCHASING_PO'),
                'url'       => url_to('purchase_order.home'),
                'class'     => (url_is('purchase-orders') ? 'active' : ''),
                'icon'      => 'fas fa-shopping-basket',
            ],
            'SETTINGS_GENERAL_INFO'  => [
                'menu'      => 'SETTINGS', // Leave empty if none
                'name'      => get_modules('SETTINGS_GENERAL_INFO'),
                'url'       => url_to('general_info.home'),
                'class'     => (url_is('settings/general-info') ? 'active' : ''),
                'icon'      => 'fas fa-info-circle',
            ],
            'EXPORT_DATA'  => [
                'menu'      => 'REPORTS', // Leave empty if none
                'name'      => get_modules('EXPORT_DATA'),
                'url'       => url_to('export.home'),
                'class'     => (url_is('reports/export') ? 'active' : ''),
                'icon'      => 'fas fa-file-export',
            ],
            'PAYROLL_SALARY_RATES'  => [
                'menu'      => 'HUMAN_RESOURCE', // Leave empty if none
                'name'      => get_modules('PAYROLL_SALARY_RATES'),
                'url'       => url_to('payroll.salary_rate.home'),
                'class'     => (url_is('payroll/salary-rates') ? 'active' : ''),
                'icon'      => 'fas fa-dollar-sign',
            ],
            'PAYROLL_PAYSLIP'  => [
                'menu'      => 'HUMAN_RESOURCE', // Leave empty if none
                'name'      => get_modules('PAYROLL_PAYSLIP'),
                'url'       => url_to('payroll.payslip.home'),
                'class'     => (url_is('payroll/payslip') ? 'active' : ''),
                'icon'      => 'fas fa-receipt',
                'header'    => 'PAYROLL',
            ],
            'PAYROLL_COMPUTATION'  => [
                'menu'      => 'HUMAN_RESOURCE', // Leave empty if none
                'name'      => get_modules('PAYROLL_COMPUTATION'),
                'url'       => url_to('payroll.computation.home'),
                'class'     => (url_is('payroll/computation') ? 'active' : ''),
                'icon'      => 'fas fa-calculator',
                'header'    => 'PAYROLL',
            ],
            'PAYROLL_LEAVE'  => [
                'menu'      => 'HUMAN_RESOURCE', // Leave empty if none
                'name'      => get_modules('PAYROLL_LEAVE'),
                'url'       => url_to('payroll.leave.home'),
                'class'     => (url_is('payroll/leave') ? 'active' : ''),
                'icon'      => 'fas fa-folder-open',
                'header'    => 'PAYROLL',
            ],
            'PAYROLL_OVERTIME'  => [
                'menu'      => 'HUMAN_RESOURCE', // Leave empty if none
                'name'      => get_modules('PAYROLL_OVERTIME'),
                'url'       => url_to('payroll.overtime.home'),
                'class'     => (url_is('payroll/overtime') ? 'active' : ''),
                'icon'      => 'fas fa-stopwatch',
                'header'    => 'PAYROLL',
            ],
            'PAYROLL_SETTINGS'  => [
                'menu'      => 'HUMAN_RESOURCE', // Leave empty if none
                'name'      => get_modules('PAYROLL_SETTINGS'),
                'url'       => url_to('payroll.settings.home'),
                'class'     => (url_is('payroll/settings') ? 'active' : ''),
                'icon'      => 'fas fa-tools',
                'header'    => 'PAYROLL',
            ],
            'PAYROLL_TIMESHEETS'  => [
                'menu'      => 'HUMAN_RESOURCE', // Leave empty if none
                'name'      => get_modules('PAYROLL_TIMESHEETS'),
                'url'       => url_to('payroll.timesheet.home'),
                'class'     => (url_is('payroll/timesheets') ? 'active' : ''),
                'icon'      => 'fas fa-user-clock',
                'header'    => 'PAYROLL',
            ],
            'FINANCE_BILLING_INVOICE'  => [
                'menu'      => 'FINANCE', // Leave empty if none
                'name'      => get_modules('FINANCE_BILLING_INVOICE'),
                'url'       => url_to('finance.billing_invoice.home'),
                'class'     => (url_is('finance/billing-invoice') ? 'active' : ''),
                'icon'      => 'fas fa-file-invoice',
            ],
            'FINANCE_FUNDS'  => [
                'menu'      => 'FINANCE', // Leave empty if none
                'name'      => get_modules('FINANCE_FUNDS'),
                'url'       => url_to('finance.funds.home'),
                'class'     => (url_is('finance/funds') ? 'active' : ''),
                'icon'      => 'fas fa-piggy-bank',
            ],
            'INVENTORY_ORDER_FORMS'  => [
                'menu'      => 'INVENTORY', // Leave empty if none
                'name'      => get_modules('INVENTORY_ORDER_FORMS'),
                'url'       => url_to('inventory.order_form.home'),
                'class'     => (url_is('inventory/order-forms') ? 'active' : ''),
                'icon'      => 'fab fa-wpforms',
            ],
        ];

        return $param ? $modules[$param] : $modules;
	}
}

if (! function_exists('get_sidebar_menus'))
{
    /**
     * Format the sidebar menus of the current user
     */
	function get_sidebar_menus()
	{
        $html           = '';
        $items          = [];
        $menus          = [];
        $headers        = [];
        $modules        = setup_modules();
        $permissions    = get_permissions();
        $user_modules   = get_user_modules($permissions);
        $permissions    = format_results($permissions, 'module_code', 'permissions');

		if (! empty($modules)) {
            ksort($modules);
            
            foreach ($modules as $key => $module) {
                if (in_array($key, $user_modules) && !empty($module)) {
                    $_perms = isset($permissions[$key]) ? $permissions[$key] : get_generic_modules_actions($key);
                    $_perms = is_array($_perms) ? $_perms : explode(',', $_perms);

                    // Check if actions VIEW OR VIEW_ALL are in the $_perms
                    $value  = array_intersect([ACTION_VIEW, ACTION_VIEW_ALL], $_perms);
    
                    // If user is not an admin and has VIEW permission
                    // for this module, then continue to the next loop
                    if (! is_admin() && empty($value)) {
                        continue;
                    }

                    $header     = $module['header'] ?? '';
                    $_header    = '';

                    if (! empty($header) && ! in_array($header, $headers)) {
                        $headers[$header]   = $header;
                        $_header            = '<li class="nav-header">'.strtoupper($header).'</li>';
                    }

                    if (! empty($module['menu'])) {                        
                        $_menu  = $module['menu'];
                        $_key   = $_menu;

                        // Check if module has nav menu
                        if (! in_array($module['menu'], $menus)) {
                            $head   = get_nav_menus($module['menu']);
                            $menu   = $head['urls'] ? 'menu-open' : '';
                            $active = $head['urls'] ? 'active' : '';

                            // Create the nav menu item
                            $menus[$module['menu']] = <<<EOF
                                <li class="nav-item {$menu}" id="{$module['menu']}">
                                    <a href="#" class="nav-link {$active}">
                                        <i class="nav-icon {$head['icon']}"></i>
                                        <p>
                                            {$head['name']}
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                            EOF;
                        }
                    } else $_key = $key;

                    // Html for nav item
                    $_item  = <<<EOF
                        {$_header}
                        <li class="nav-item" id="{$key}">
                            <a href="{$module['url']}" class="nav-link {$module['class']}">
                                <i class="nav-icon {$module['icon']}"></i>
                                <p>
                                    {$module['name']}
                                </p>
                            </a>
                        </li>
                    EOF;

                    // Concat to $html if $_key is false
                    if (strpos($html, $_key) === false)  $html .= "[$_key]";
                    
                    // Append to $items
                    if (isset($items[$_key])) {
                        // Concat if $_key does exist
                        $items[$_key] .= $_item;
                    } else $items[$_key] = $_item;
                }
            }

            if (! empty($menus)) {
                // Check if the $menus has value
                foreach ($menus as $key => $val) {
                    if (isset($items[$key])) {
                        $mod = <<<EOF
                            {$val}
                             <ul class="nav nav-treeview">
                                 {$items[$key]}
                             </ul>
                         EOF;
                        // If key is in the item replace it
                        $html = str_replace("[$key]", $mod, $html);
                    }
                }
            }

            if (! empty($items)) {
                // For nav item that has no nav header
                foreach ($items as $key => $val) {
                    if (strpos($html, $key)) {
                        $html = str_replace("[$key]", $val, $html);
                    }
                        
                }
            }

        }

        return $html;
	}
}
