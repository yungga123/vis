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
	 * Get the module list
	 */
	function get_modules(string|null $param = null): string|array
	{
		$modules = MODULES;

        asort($modules);

        // if(! is_admin()) unset($modules['SETTINGS_MAILCONFIG']);

		return $param ? $modules[strtoupper($param)] : $modules;
	}
}

if (! function_exists('get_actions'))
{
	/**
	 * Get the action list
	 */
	function get_actions(string $param = null, bool $withOthers = false): string|array
	{
		$actions = ACTIONS;

		if ($param && !in_array($param, $actions)) {
			$others = array_values($actions['OTHERS']);
			for ($i=0; $i <= count($others); $i++) { 
				if (isset($others[$i][$param])) return $others[$i][$param];
			}
		}

        if (! $withOthers) unset($actions['OTHERS']);

		return $param ? $actions[strtoupper($param)] : $actions;
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
        $profile = new \App\Controllers\AccountProfile();
        return $profile->getProfileImg(session('gender'));
	}
}

if (! function_exists('get_employees'))
{
	/**
	 * Get employees - default columns (id, name) only
	 * @param int|null $id [optional]
	 * @param string|array $columns [optional]
	 */
	function get_employees(int $id = null, string|array $columns = []): array 
	{
		$columns 	= !empty($columns) ? $columns : "employee_id, CONCAT(firstname,' ',lastname) AS employee_name";
		$model 		= new \App\Models\EmployeesModel();
        $builder 	= $model->select($columns);

		$builder->where('employee_id !=', 'SOFTWAREDEV');
		$builder->orderBy('employee_name ASC');
		
		return $id ? $builder->find() : $builder->findAll();
	}
}