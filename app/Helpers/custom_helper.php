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
        $is_sales = (
            url_is('customers') || 
            url_is('customers/commercial') || 
            url_is('customers/residential') || 
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
        return strtoupper(session('access_level')) === strtoupper(AAL_ADMIN);
	}
}

/**
 * Check if current logged user is executive
 */
if (! function_exists('is_executive'))
{
	function is_executive(): bool
	{
        return strtoupper(session('access_level')) === strtoupper(AAL_EXECUTIVE);
	}
}

/**
 * Check if current logged user is manager
 */
if (! function_exists('is_manager'))
{
	function is_manager(): bool
	{
        return strtoupper(session('access_level')) === strtoupper(AAL_MANAGER);
	}
}

/**
 * Get role / access level list
 */
if (! function_exists('get_roles'))
{
	function get_roles(string|null $param = null): string|array
	{
		$model = new \App\Models\RolesModel();
        $roles = $model->getRoles();
        
        if (! empty($roles)) {
            if(! is_admin()) unset($roles['ADMIN']);

		    return $param ? $roles[strtoupper($param)] : $roles;
        }
        
        return false;
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

		return $param ? $modules[strtoupper($param)] : $modules;
	}
}

/**
 * Get the action list
 */
if (! function_exists('get_actions'))
{
	function get_actions(string|null $param = null, bool $in_out = false): string|array
	{
		$actions = ACTIONS;

        if ($in_out) $actions += ['ITEM_IN' => 'Item In', 'ITEM_OUT' => 'Item Out'];

		return $param ? $actions[strtoupper($param)] : $actions;
	}
}

/**
 * Check the permissions if user can add/edit/delete
 */
if (! function_exists('check_permissions'))
{
	function check_permissions(array $permissions, string $needle): bool
	{
		return in_array($needle, $permissions) || is_admin();
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
                'supervisor_sales'     => 'Sales Supervisor',
                'supervisor_inventory'     => 'Inventory',
                'supervisor_project'     => 'Project Engineer',
                'supervisor_purchasing'     => 'Purchasing',
                'supervisor_hr'     => 'HR Staff',
                'supervisor_it'     => 'IT Head',
                'manager_technical'     => 'Technical Manager',
                'manager_admin'     => 'Admin Manager',
                'manager_sales'     => 'Sales Manager',
                'manager_hr'     => 'HR Manager',
                'manager_accounting'     => 'Accounting Manager',
                'manager_finance'     => 'Finance Manager',
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
        $profile = new \App\Controllers\AccountProfile();
        return $profile->getProfileImg(session('gender'));
	}
}

/**
 * Get inventory dropdowns
 */
if (! function_exists('inventory_categories_options'))
{
	function inventory_categories_options($model, $all = true) 
	{
		$option     = '';
        $others     = '';
        $columns    = 'dropdown_id, dropdown, other_category_type';
        $categories = $model->getDropdowns('CATEGORY', $columns, $all);

        if (! empty($categories)) {
            foreach ($categories as $category) {
                if (empty($category['other_category_type'])) {
                    $option     .= '
                        <option value="'. $category['dropdown_id'] .'">
                            '. $category['dropdown'] .'
                        </option>
                    ';
                } else {
                    $others     .= '
                        <option value="other__'. $category['dropdown_id'] .'">
                            '. $category['dropdown'] .'
                        </option>
                    ';
                }
            }

            $option .= '<optgroup label="Other Categories">'. $others .'</optgroup>';
        }

        return $option;
    }
}

/**
 * Check string if contains the passed value
 */
if (! function_exists('check_string_contains'))
{
	function check_string_contains(string $string, string $val): bool
	{
        return (strpos($string, $val) !== false);
	}
}

/**
 * DataTable html button format
 */
if (! function_exists('dt_button_html'))
{
	function dt_button_html(array $options, bool $dropdown = false): string
	{     
        $wfull  = $dropdown ? 'w-100' : '';  
        $html   = <<<EOF
            <button class="btn btn-sm {$options['button']} {$wfull}" {$options['condition']}>
                <i class="{$options['icon']}"></i> {$options['text']}
            </button>
        EOF;

        return $dropdown 
            ? '<div class="dropdown-item">'. $html .'</div>'
            : $html;
	}
}

/**
 * DataTable buttons actions format
 */
if (! function_exists('dt_button_actions'))
{
	function dt_button_actions(array $row, string $id, array $permissions, bool $dropdown = false): string
	{
        $options    = [
            'edit' => [
                'text'      => '',
                'button'    => 'btn-warning',
                'icon'      => 'fas fa-edit',
                'condition' => 'title="Cannot edit" disabled',
            ],
            'delete' => [
                'text'      => '',
                'button'    => 'btn-danger',
                'icon'      => 'fas fa-trash',
                'condition' => 'title="Cannot delete" disabled',
            ],
        ];
            
        if (check_permissions($permissions, 'EDIT')) {
            $options['edit']['text']        = $dropdown ? 'Edit' : '';
            $options['edit']['condition']   = 'onclick="edit('.$row["$id"].')" title="Edit"';
        }
            
        if (check_permissions($permissions, 'DELETE')) {
            $options['delete']['text']        = $dropdown ? 'Delete' : '';
            $options['delete']['condition']   = 'onclick="remove('.$row["$id"].')" title="Delete"';
        }

        $html = dt_button_html($options['edit'], $dropdown);
        $html .= dt_button_html($options['delete'], $dropdown);

        return $html;
	}
}

/**
 * DataTable buttons dropdown html format
 */
if (! function_exists('dt_buttons_dropdown'))
{
	function dt_buttons_dropdown(string $buttons, bool $dropdown = false): string
	{   
        $buttons = $dropdown ? $buttons : '<div class="dropdown-item">'.$buttons.'</div>';
        return <<<EOF
            <div class="">
                <button class="btn btn-info btn-sm dropdown-toggle rounded" type="button" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-info-circle"></i> Actions</button>
                <div class="dropdown-menu">
                    {$buttons}
                </div>
            </div>
        EOF;
	}
}

/**
 * Clear variable based on the passed params
 */
if (! function_exists('remove_string'))
{
	function remove_string(string|array $subject, string $search, string $replace = ''): string|array
	{
        if (is_array($subject)) {
            $arr = [];
            foreach ($subject as $val) {
                $arr[] = str_replace($search, $replace, $val);
            }

            $subject = $arr;
        } else {
            $subject = str_replace($search, $replace, $subject);
        }

        return $subject;
	}
}