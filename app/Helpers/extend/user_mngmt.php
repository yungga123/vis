<?php
if (! function_exists('get_permissions'))
{
	/**
	 * Get the permissions of the current logged user
	 */
	function get_permissions(): array 
	{
		$model = new \App\Models\PermissionModel();
        return $model->getCurrUserPermissions();
	}
}

if (! function_exists('is_developer'))
{
	/**
	 * Check if current logged user is the developer
	 */
	function is_developer(): bool
	{
        return (
			strtoupper(session('access_level')) === strtoupper(AAL_ADMIN) &&
			session('username') == DEVELOPER_USERNAME &&
			session('employee_id') == DEVELOPER_ACCOUNT
		);
	}
}

if (! function_exists('is_admin'))
{
	/**
	 * Check if current logged user is administration
	 */
	function is_admin(): bool
	{
        return strtoupper(session('access_level')) === strtoupper(AAL_ADMIN);
	}
}

if (! function_exists('is_executive'))
{
	/**
	 * Check if current logged user is executive
	 */
	function is_executive(): bool
	{
        return strtoupper(session('access_level')) === strtoupper(AAL_EXECUTIVE);
	}
}

if (! function_exists('is_manager'))
{
	/**
	 * Check if current logged user is manager
	 */
	function is_manager(): bool
	{
        return strtoupper(session('access_level')) === strtoupper(AAL_MANAGER);
	}
}

if (! function_exists('get_roles'))
{
	/**
	 * Get role / access level list
	 */
	function get_roles(string $param = null, $is_option = false): string|array
	{
		$model = new \App\Models\RolesModel();
        $roles = $model->getRoles();
        
        if (! empty($roles)) {
            if(! is_admin() && $is_option) unset($roles['ADMIN']);

		    return $param ? $roles[strtoupper($param)] : $roles;
        }
        
        return [];
	}
}

if (! function_exists('get_roles_options'))
{
	/**
	 * Get roles for options selection
	 */
	function get_roles_options(): string
	{
		$html 	= '';
        $roles 	= get_roles();
        
        if (! empty($roles)) {
			$options = [];

            foreach ($roles as $key => $role) {
				$level = str_contains($key,'MANAGER') ? 'MANAGER' : (str_contains($key,'SUPERVISOR') ? 'SUPERVISOR' : 'OTHERS');

				$options[$level][] = <<<EOF
					<option value="{$key}">{$role}</option>
				EOF;
			}

			$managers 		= implode('', $options['MANAGER']);
			$supervisors 	= implode('', $options['SUPERVISOR']);
			$others 		= implode('', $options['OTHERS']);

			$html = <<<EOF
				<optgroup label="Managerial Level">
					{$managers}
				</optgroup>
				<optgroup label="Supervisory Level">
					{$supervisors}
				</optgroup>
				<optgroup label="Others">
					{$others}
				</optgroup>
			EOF;
        }
        
        return $html;
	}
}

if (! function_exists('get_modules'))
{
	/**
	 * Get the modules list
	 */
	function get_modules(string|null $param = null): string|array
	{
		$modules 	= MODULES;
		$param		= $param ? strtoupper($param) : $param;

        asort($modules);

        // if(! is_admin()) unset($modules['SETTINGS_MAILCONFIG']);

		return $param 
			? (isset($modules[$param]) ? $modules[$param] : '')
		 	: $modules;
	}
}

if (! function_exists('get_modules_options'))
{
	/**
	 * Get modules for options selection
	 */
	function get_modules_options(): string
	{
		$html 	= '';
        $modules 	= get_modules();
        
        if (! empty($modules)) {
			$options 	= [];
			$setups		= setup_modules();
			$menus		= get_nav_menus();

			// Exclude dashboard
			unset($modules['DASHBOARD']);

            foreach ($modules as $key => $module) {
				$setup 		= $setups[$key];
				$menu_code 	= $setup['menu'];
				$menu_name 	= isset($menus[$menu_code]) ? $menus[$menu_code]['name'] : $module;

				// Store option with menu/module name as key
				$options[$menu_name][] = <<<EOF
					<option value="{$key}">{$module}</option>
				EOF;
			}

			if (! empty($options)) {
				ksort($options);

				foreach ($options as $key => $vals) {
					// Make the key as opt group label
					$html .= "<optgroup label='{$key}'>";

					foreach ($vals as $val) {
						$html .= $val;
					}

					$html .= "</optgroup>";
				}
			}
        }
        
        return $html;
	}
}

if (! function_exists('get_module_codes'))
{
	/**
	 * Get the module codes list
	 */
	function get_module_codes(string|null $param = null): string|array
	{
		$module_codes = MODULE_CODES;
		$param			= $param ? strtolower($param) : $param;

		return $param 
			? (isset($module_codes[$param]) ? $module_codes[$param] : '')
		 	: $module_codes;
	}
}

if (! function_exists('get_actions'))
{
	/**
	 * Get the action list
	 */
	function get_actions(string $param = null, bool $with_others = false, bool $with_generic = false): string|array
	{
		$actions = ACTIONS;

		if ($param && !array_key_exists($param, $actions)) {
			$others 	= $actions['OTHERS'];
			
			// Check if param is a module code
			if (isset($others[$param])) {
				$param 	= $others[$param];

				if (isset($param['OTHERS_ONLY'])) {
					unset($param['OTHERS_ONLY']);

					if (! $with_generic) return $param;
				}

				unset($actions['OTHERS']);

				return array_merge($actions, $param);
			}

			$others_val = array_values($actions['OTHERS']);

			for ($i=0; $i <= count($others_val); $i++) { 
				if (isset($others_val[$i][$param])) {
					return $others_val[$i][$param];
				}
			}
		}

        if (! $with_others) unset($actions['OTHERS']);

		if ($param && isset($actions[strtoupper($param)])) {
			return $actions[strtoupper($param)];
		}

		return $actions;
	}
}

if (! function_exists('get_generic_modules_actions'))
{
	/**
	 * Get the actions of modules with generic acess
	 */
	function get_generic_modules_actions(string $param = null): string|array|bool
	{
		$modules = MODULES_WITH_GENERIC_ACCESS;

		if (in_array($param, $modules) || isset($modules[$param])) {
			$module 	= $modules[$param] ?? $param;
			$generic 	= array_keys(get_actions());
			
			if (is_array($module)) {
				$generic = array_diff($generic, $module['EXCEPT']);
			}
			
			return $generic;
		}

		if (! empty($modules) && ! $param) {
			$_modules = [];

			foreach ($modules as $key => $val) {
				$_modules[] = is_array($val) ? $key : $val;
			}

			return $_modules;
		}

		return $param ? [] : $modules;
	}
}

if (! function_exists('check_permissions'))
{
	/**
	 * Check the permissions if user can add/edit/delete
	 */
	function check_permissions(array $permissions, string $needle): bool
	{
		return in_array($needle, $permissions) || is_admin();
	}
}

/* Deprecated - use get_roles() */
if (! function_exists('account_access_level'))
{
	/**
	 * Access level
	 */
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

if (! function_exists('get_avatar'))
{
	/**
	 * Get the avatar of the current user
	 */
	function get_avatar(string|null $param = null): string|array
	{
		$avatars = [
			'female' 	=> 'assets/dist/img/avatar3.png',
			'male' 		=> 'assets/dist/img/avatar5.png',
		];

		return $param ? $avatars[strtolower($param)] : $avatars;
	}
}

if (! function_exists('get_current_user_avatar'))
{
	/**
	 * Get the avatar of the current logged user
	 */
	function get_current_user_avatar(): string
	{
        $profile = new \App\Controllers\HR\AccountProfile();
        return $profile->getProfileImg(session('gender'));
	}
}

if (! function_exists('get_employees'))
{
	/**
	 * Get employees - default columns (id, name) only
	 * 
	 * @param int|null $id [optional]
	 * @param string|array $columns [optional]
	 * @param bool $with_resign [optional]
	 * 
	 * @return array
	 */
	function get_employees(int $id = null, string|array $columns = [], $without_resign = false): array 
	{
		$columns 	= !empty($columns) ? $columns : "employee_id, CONCAT(firstname,' ',lastname) AS employee_name";
		$model 		= new \App\Models\EmployeeModel();
        $builder 	= $model->select($columns);

		if (! is_developer()) $builder->where('employee_id !=', DEVELOPER_ACCOUNT);

		// Whether to not include resigned employees
		// Default - resigned are included
		if ($without_resign) $model->withOutResigned($builder);
		
		$builder->orderBy('employee_name ASC');

		if ($id) {
			if (is_string($id) && strpos($id, ',') === false) {
				$builder->where('employee_id', $id);
				
				return $builder->first();
			}

			if (is_array($id) && !empty($id)) {
				foreach ($id as $key => $val) {
					if (is_numeric($val)) {
						$builder->where('id', $val);
					} else {
						$builder->where('employee_id', $val);
					}
				}

				return $builder->findAll();
			}
		}
		
		return $id ? $builder->find($id) : $builder->findAll();
	}
}