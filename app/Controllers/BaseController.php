<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['url', 'form', 'formatter', 'custom'];


    /**
     * Add custom properties accessible throughout all controllers
     */
    // Will be use to initialize \Config\Database::connect();
    protected $builder;

    // Use to get the user permissions
    protected $permissions;

    // Use to get the user modules
    protected $modules;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();

        $this->builder          = \Config\Database::connect();
        $this->permissions      = url_is('login') ? [] : get_permissions();
        $this->modules          = get_user_modules($this->permissions);
    }

    /**
     * DB transaction begin for the default database
     */
    protected function transBegin()
    {
        $this->builder->transBegin();
    }

    /**
     * DB transaction commit for the default database
     */
    protected function transCommit()
    {
        $this->builder->transCommit();
    }

    /**
     * DB transaction rollback for the default database
     */
    protected function transRollback()
    {
        $this->builder->transRollback();
    }

    /**
     * Check if current logged user is administrator
     * 
     * @return bool
     */
    protected function isAdmin()
    {
        return session('access_level') === AAL_ADMIN;
    }

    /**
     * Get specific permissions based on module code
     *
     * @param string $module_code
     * 
     * @return string|array
     */
    protected function getSpecificPermissions($module_code)
	{
        if ($this->isAdmin()) return array_keys(ACTIONS);

        $model = new \App\Models\PermissionModel();
        $perms = $model->getCurrUserSpecificPermissions($module_code);

        if (empty($perms)) return false;
        
		return explode(',', $perms['permissions']);
	}

    /**
     * Get specific permissions from $this->permissions based on module code
     *
     * @param string $module_code
     * 
     * @return array
     */
    protected function getSpecificActionsByModule($module_code)
	{
        if ($this->isAdmin()) {
            // Get the array keys only
            $actions        = array_keys(get_actions());
            // Get the actions under OTHERS key
            $action_others  = get_actions('OTHERS', true);
            // Check if module has other actions
            $is_exist       = array_key_exists($module_code, $action_others);
            // Get the array keys only
            $action_others  =  $is_exist ? array_keys($action_others[$module_code]) : $action_others;
            
            return $is_exist 
                ? array_merge($actions, $action_others) : $actions;
        }

        foreach ($this->permissions as $permission) {
            if ($permission['module_code'] === $module_code) 
                return explode(',', $permission['permissions']);
        }
        
		return [];
	}

    /**
     * Check permissions based on the passed needle
     *
     * @param string|array $permissions
     * @param string $needle
     * 
     * @return bool
     */
    protected function checkPermissions($permissions, $needle)
	{
        if ($this->isAdmin()) return true;
        if ($permissions) {
		    return in_array($needle, $permissions);
        }

        return false;
	}

    /**
     * Check role permissions based on the passed arguments
     * and redirect to denied page
     * 
     * @param string $module
     * @param string $action
     * 
     * @return void|view
     */
    public function checkRolePermissions($module, $action = null)
	{
        if ($this->isAdmin()) return true;
        if ($action) $this->checkRoleActionPermissions($module, $action);
		if (! in_array($module, $this->modules)) {
            $this->redirectToAccessDenied();
        }
	}

    /**
     * Check role & action permissions based on the passed argument
     * and redirect to denied page if user don't have
     * 
     * @param string $module
     * @param string|array $action
     * @param bool $throwException  Whether to throw an exception or the default return
     * 
     * @return void|view|\Exception
     */
    public function checkRoleActionPermissions($module, $action, $throwException = false)
	{
        $module     = $module ? strtoupper($module) : $module;
		$this->checkRolePermissions($module);
        // If has access in the module, then check 
        // if user has the specific permission/action
        // Ex. User has access to Dispatch but don't have permission for printing
        $action     = clean_param($action, 'strtoupper');
        $action     = is_string($action) ? [$action] : $action;
        $actions    = $this->getSpecificActionsByModule($module);
        // Add the default PENDING
        $actions[]  = 'PENDING';

        // Check if user has the permission 
        // to perform that $action
        $value = array_intersect($action, $actions);

        if (empty($value)) {
            if ($throwException) {
                throw new \Exception(res_lang('restrict.permission.change', implode(', ', $action)), 2);
            }

            $this->redirectToAccessDenied();
        }
	}

    /**
     * Redirect to access denied view
     * 
     * @return view
     */
    public function redirectToAccessDenied()
	{
		$data['title']          = 'Access Denied';
        $data['page_title']     = 'Access Denied!';

        echo view('errors/custom/denied', $data);
        exit;
	}

    /**
     * The custom try catch function for handling error
     * 
     * @param array $data           The $data variable from the parent method
     * @param callable $callback    The callback function where logic is
     * @param bool $dbTrans         [Optional - default true] The identifier if will use db transactions
     * 
     * @return array|object         The passed/response $data variable
     */
    public function customTryCatch($data, $callback, $dbTrans = true)
	{
        // Using DB Transaction
        if ($dbTrans) $this->transBegin();

        try {
            $data = $callback($data);

            // Commit transaction
            if ($dbTrans) $this->transCommit();
        } catch (\Exception $e) {
            // Try catch exception error handling
            $data = $this->tryCatchException($data, $e, $dbTrans);
        }

        return $this->response->setJSON($data);
	}

    /**
     * The common try catch exception method in handling error
     * 
     * @param array $data   The $data variable from the parent method
     * @param object $e     The exception object
     * 
     * @return array        The passed $data variable
     */
    public function tryCatchException($data, $e, $dbTrans = true)
	{
        // Rollback transaction if there's an error
        if ($dbTrans) $this->transRollback();

		$this->logExceptionError($e, __METHOD__);
        
        $data['status']     = res_lang('status.error');
        $data['message']    = $e->getCode() > 0 
            ? $e->getMessage() : res_lang('error.process');

        return $data;
	}

    /**
     * Log the exception error
     * 
     * @param array|object $exception
     * @param string $method
     * 
     * @return void 
     */
    public function logExceptionError($exception, $method = null)
	{
		log_msg(
            "Exception Error: {exception} \nMethod: '{method}'", 
            ['exception' => $exception, 'method' => $method ?? __METHOD__]
        );
	}
}
