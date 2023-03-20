<?php

/**
 * Get the permissions of the current logged user
 */
if (! function_exists('get_permissions'))
{
	function get_permissions(): array 
	{
		$model = new \App\Models\PermissionModel();
        return $model->getCurrUserPermissions();
	}
}

/**
 * Get the modules of the current logged user
 */
if (! function_exists('get_user_modules'))
{
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

/**
 * Get the nav menus of some of the modules
 */
if (! function_exists('get_nav_menus'))
{
	function get_nav_menus(string $param): array
	{
		$menu = [
            'SALES'            => [
                'name'      => 'Sales',
                // Level two urls (modules) - need to add ||/OR in every new module
                'urls'      => (url_is('customers') || url_is('customers/commercial') || url_is('customers/residential') || url_is('tasklead')),
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
                'urls'      => (url_is('settings/mail') || url_is('settings/permissions')),
                'icon'      => 'fas fa-cog',
            ],
        ];

        return $menu[$param];
	}
}

/**
 * Setup modules - like icons, urls etc..
 */
if (! function_exists('setup_modules'))
{
	function setup_modules(string $param = null)
	{
		$modules = [
            // 'DASHBOARD'             => 'Dashboard',
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
            // 'CUSTOMERS'             => [
            //     'menu'      => 'SALES', // Leave empty if none
            //     'name'      => get_modules('CUSTOMERS'),
            //     'url'       => url_to('customers.home'),
            //     'class'     => (url_is('customers') ? 'active' : ''),
            //     'icon'      => 'far fa-address-card',
            // ],
            'CUSTOMERS_COMMERCIAL'             => [
                'menu'      => 'SALES', // Leave empty if none
                'name'      => get_modules('CUSTOMERS_COMMERCIAL'),
                'url'       => url_to('customervt.home'),
                'class'     => (url_is('customers/commercial') ? 'active' : ''),
                'icon'      => 'far fa-address-card',
            ],
            'CUSTOMERS_RESIDENTIAL'             => [
                'menu'      => 'SALES', // Leave empty if none
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
            // 'MANAGER_OF_SALES'      => [
            //     'menu'      => 'SALES', // Leave empty if none
            //     'name'      => get_modules('MANAGER_OF_SALES'),
            //     'url'       => '#',
            //     'class'     => (url_is('employees') ? 'active' : ''),
            //     'icon'      => 'far fa-circle',
            // ],
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
        ];

        return $param ? $modules[$param] : $modules;
	}
}

/**
 * Format the sidebar menus of the current user
 */
if (! function_exists('get_sidebar_menus'))
{
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

/**
 * Check if current logged user is administration
 */
if (! function_exists('is_admin'))
{
	function is_admin(): bool
	{
        return session('access_level') === AAL_ADMIN;
	}
}

/**
 * Get role / access level list
 */
if (! function_exists('get_roles'))
{
	function get_roles(string|null $param = null): string|array
	{
		$roles = ROLES;
        
        if(! is_admin()) unset($roles['ADMIN']);

		return $param ? $roles[$param] : $roles;
	}
}

/**
 * Get the module list
 */
if (! function_exists('get_modules'))
{
	function get_modules(string|null $param = null): string|array
	{
		$modules = MODULES;

        asort($modules);

        // if(! is_admin()) unset($modules['SETTINGS_MAILCONFIG']);

		return $param ? $modules[$param] : $modules;
	}
}

/**
 * Get the action list
 */
if (! function_exists('get_actions'))
{
	function get_actions(string|null $param = null): string|array
	{
		$actions = ACTIONS;

		return $param ? $actions[$param] : $actions;
	}
}

/**
 * Check the permissions if user can add/edit/delete
 */
if (! function_exists('check_permissions'))
{
	function check_permissions(array $permissions, string $needle): bool
	{
		return in_array($needle, $permissions) ? true : false;
	}
}

/**
 * Access level
 */
if (! function_exists('account_access_level'))
{
	function account_access_level($old = false, $params = null): mixed
	{
		$access_levels = $old 
			? [
				'admin' 		=> 'Administrator',
				'manager' 		=> 'Manager',
				'sales' 		=> 'Sales',
				'ofcadmin' 		=> 'Office Admin',
				'hr' 			=> 'HR',
				'user'  		=> 'User',
			] 
			: [
				// 'super_admin' 	=> 'Super Admin',
				'admin' 		=> 'Administrator',
				'executive' 	=> 'Executive',
				'manager' 		=> 'Manager',
				'operation' 	=> 'Admin/Operation',
				'supervisor'	=> 'Supervisory',
				'user'  		=> 'General User',
			];

		if (! empty($params)) {
			if (is_string($params)) {
				return $access_levels[$params];
			} 

			if (is_array($params)) {
				$arr = [];
				foreach ($access_levels as $key => $val) {
					if (in_array($key, $params)) {
						$arr[$key] = $val;
					}
				}

				return $arr;
			}
		}

		return $access_levels;
	}
}

/**
 * Get the avatar of the current user
 */
if (! function_exists('get_avatar'))
{
	function get_avatar(string|null $param = null): string|array
	{
		$avatars = [
			'female' 	=> 'assets/dist/img/avatar3.png',
			'male' 		=> 'assets/dist/img/avatar5.png',
		];

		return $param ? $avatars[strtolower($param)] : $avatars;
	}
}

/**
 * Get the avatar of the current logged user
 */
if (! function_exists('get_current_user_avatar'))
{
	function get_current_user_avatar(): string
	{
        $gender = strtolower(session('gender'));
		return base_url(get_avatar($gender));
	}
}