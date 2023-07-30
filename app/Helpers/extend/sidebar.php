<?php
if (! function_exists('get_user_modules'))
{
    /**
     * Get the modules of the current logged user
     */
	function get_user_modules(array|null $permissions = null): array 
	{
        if (session('access_level') === AAL_ADMIN) {
            return array_keys(MODULES);
        }

        $permissions = $permissions ?? get_permissions();
		return !empty($permissions) 
                ? array_column($permissions, 'module_code') : [];
	}
}

if (! function_exists('get_nav_menus'))
{
    /**
     * Get the nav menus of some of the modules
     */
	function get_nav_menus(string $param): array
	{
        $is_sales = (
            url_is('tasklead') || 
            url_is('sales_manager') || 
            url_is('sales_manager_indv')
        );
		$menu = [
            'SALES'            => [
                'name'      => 'Sales',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => $is_sales,
                'icon'      => 'far fa-credit-card',
            ],
            'HUMAN_RESOURCE'   => [
                'name'      => 'Human Resource',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => (url_is('accounts') || url_is('employees')),
                'icon'      => 'fas fa-users',
            ],
            'SETTINGS'          => [
                'name'      => 'Settings',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => (url_is('settings/mail') || url_is('settings/permissions') || url_is('settings/roles')),
                'icon'      => 'fas fa-cog',
            ],
            'PURCHASING'          => [
                'name'      => 'Purchasing',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => (url_is('suppliers')),
                'icon'      => 'fas fa-money-check',
            ],
            'ADMIN'          => [
                'name'      => 'Admin',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => (url_is('job-orders') || url_is('schedules') || url_is('dispatch')),
                'icon'      => 'fas fa-users-cog',
            ],
            'CLIENTS'          => [
                'name'      => 'Clients',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => (url_is('customers') || url_is('customers/commercial') || url_is('customers/residential')),
                'icon'      => 'fas fa-clipboard-list',
            ],
        ];

        return $menu[$param];
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
            'CUSTOMERS_COMMERCIAL'             => [
                'menu'      => 'CLIENTS', // Leave empty if none
                'name'      => get_modules('CUSTOMERS_COMMERCIAL'),
                'url'       => url_to('customervt.home'),
                'class'     => (url_is('customers/commercial') ? 'active' : ''),
                'icon'      => 'far fa-address-card',
            ],
            'CUSTOMERS_RESIDENTIAL'             => [
                'menu'      => 'CLIENTS', // Leave empty if none
                'name'      => get_modules('CUSTOMERS_RESIDENTIAL'),
                'url'       => url_to('customersresidential.home'),
                'class'     => (url_is('customers/residential') ? 'active' : ''),
                'icon'      => 'far fa-address-book',
            ],
            'TASK_LEAD'             => [
                'menu'      => 'SALES', // Leave empty if none
                'name'      => get_modules('TASK_LEAD'),
                'url'       => url_to('tasklead.home'),
                'class'     => (url_is('tasklead') ? 'active' : ''),
                'icon'      => 'fas fa-tasks',
            ],
            'MANAGER_OF_SALES'      => [
                'menu'      => 'SALES', // Leave empty if none
                'name'      => get_modules('MANAGER_OF_SALES'),
                'url'       => url_to('sales_manager.home'),
                'class'     => (url_is('sales_manager') ? 'active' : ''),
                'icon'      => 'far fa-circle',
            ],
            'MANAGER_OF_SALES_INDV'      => [
                'menu'      => 'SALES', // Leave empty if none
                'name'      => get_modules('MANAGER_OF_SALES_INDV'),
                'url'       => url_to('sales_manager_indv.home'),
                'class'     => (url_is('sales_manager_indv') ? 'active' : ''),
                'icon'      => 'far fa-circle',
            ],
            'INVENTORY'     => [
                'menu'      => '', // Leave empty if none
                'name'      => get_modules('INVENTORY'),
                'url'       => url_to('inventory.home'),
                'class'     => (url_is('inventory') || url_is('inventory/dropdowns') || url_is('inventory/logs') ? 'active' : ''),
                'icon'      => 'fas fa-shopping-cart',
            ],
            'SETTINGS_MAILCONFIG'   => [
                'menu'      => 'SETTINGS', // Leave empty if none
                'name'      => get_modules('SETTINGS_MAILCONFIG'),
                'url'       => url_to('mail.home'),
                'class'     => (url_is('settings/mail') ? 'active' : ''),
                'icon'      => 'fas fa-envelope',
            ],
            'SETTINGS_PERMISSIONS'   => [
                'menu'      => 'SETTINGS', // Leave empty if none
                'name'      => get_modules('SETTINGS_PERMISSIONS'),
                'url'       => url_to('permission.home'),
                'class'     => (url_is('settings/permissions') ? 'active' : ''),
                'icon'      => 'fas fa-user-lock',
            ],
            'SETTINGS_ROLES'   => [
                'menu'      => 'SETTINGS', // Leave empty if none
                'name'      => get_modules('SETTINGS_ROLES'),
                'url'       => url_to('roles.home'),
                'class'     => (url_is('settings/roles') ? 'active' : ''),
                'icon'      => 'fas fa-user-tag',
            ],
            'ADMIN_JOB_ORDER'   => [
                'menu'      => 'ADMIN', // Leave empty if none
                'name'      => get_modules('ADMIN_JOB_ORDER'),
                'url'       => url_to('job_order.home'),
                'class'     => (url_is('job-orders') ? 'active' : ''),
                'icon'      => 'fas fa-user-md',
            ],
            'ADMIN_SCHEDULES'   => [
                'menu'      => 'ADMIN', // Leave empty if none
                'name'      => get_modules('ADMIN_SCHEDULES'),
                'url'       => url_to('schedule.home'),
                'class'     => (url_is('schedules') ? 'active' : ''),
                'icon'      => 'fas fa-calendar-alt',
            ],
            'ADMIN_DISPATCH'   => [
                'menu'      => 'ADMIN', // Leave empty if none
                'name'      => get_modules('ADMIN_DISPATCH'),
                'url'       => url_to('dispatch.home'),
                'class'     => (url_is('dispatch') ? 'active' : ''),
                'icon'      => 'fas fa-user-astronaut',
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
        $modules        = setup_modules();
        $user_modules   = get_user_modules();

		if (! empty($modules)) {
            ksort($modules);
            foreach ($modules as $key => $module) {
                if (in_array($key, $user_modules) && !empty($module)) {

                    if (! empty($module['menu'])) {                        
                        $_menu = $module['menu'];
                        $_key = $_menu;

                        // Check if module has nav menu
                        if (!in_array($module['menu'], $menus)) {
                            $head = get_nav_menus($module['menu']);
                            $menu = $head['urls'] ? 'menu-open' : '';
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
