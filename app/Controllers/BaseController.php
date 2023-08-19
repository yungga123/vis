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
     * Check permissions based on the passed needle
     *
     * @param string|array $permissions
     * @param string $needle
     * @return bool
     */
    protected function checkPermissions($permissions, $needle)
	{
        if ($this->isAdmin()) return true;
        if ($permissions) {
		    return in_array($needle, $permissions) ? true : false;
        }

        return false;
	}

    /**
     * Check role permissions based on the passed needle
     * and redirect to denied page
     * @param string $module
     * @return view
     */
    public function checkRolePermissions($module)
	{
		if (! in_array($module, $this->modules)) {
            $data['title']          = 'Access Denied';
            $data['page_title']     = 'Access Denied!';

            echo view('errors/custom/denied', $data);
            exit;
        }
	}
}
