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
     * For sending mail to employee
     * $request param should contain [employee_id, username, password]
     * @param array $request    - should contain [employee_id, username, password]
     * @param string $sendVia   - either 'xoauth' or 'regular'
     * @param boolean $is_add   - set true if from add request
     * @return array
     */
    protected function sendMail($request, $sendVia, $is_add = false)
    {
        // Declare mail config controller
        $mail = new \App\Controllers\Settings\MailConfig();

        // Get employee details
        $employeesModel = new \App\Models\EmployeesModel();
        $params = $employeesModel->getEmployeeDetails(
            $request['employee_id'],
            'employee_id, employee_name, email_address',
        );

        $params['username'] = $request['username'];
        $params['password'] = $request['password'];
        $params['subject'] = 'Password changed confirmation!';

        if ($is_add) {
            $params['subject'] = 'Account confirmation!';
            $params['is_add'] = true;
        }

        // $params should contain (employee_id, employee_name, email_address, username, password, subject)
        // And $sendVia either via 'regular' or 'xoauth'
        $res = $mail->send($params, $sendVia);
        $status = $res['status'];
        $message = $res['message'];

        if($status === STATUS_ERROR) {
            // If mail didn't sent set status as info
            $status = STATUS_INFO;
            $message = 'Account has been updated but mail could not be sent!';
        }

        return compact('status', 'message');
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
     * @param string $permissions
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

    /**
     * The custom try catch function for handling error
     * 
     * @param array $data           The $data variable from the parent method
     * @param function $callback    The callback function where logic is
     * @param bool $dbTrans         [Optional - default true] The identifier if will use db transactions
     * @return array                The passed/response $data variable
     */
    public function customTryCatch($data, $callback, $dbTrans = true)
	{
        // Using DB Transaction
        if ($dbTrans) $this->transBegin();

        try {
            $data = $callback($data);

            // Commit transaction
            if ($dbTrans) $this->transCommit();
        } catch (\Exception$e) {
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
     * @return array        The passed $data variable
     */
    public function tryCatchException($data, $e, $dbTrans = true)
	{
        // Rollback transaction if there's an error
        if ($dbTrans) $this->transRollback();

		log_message('error', '[ERROR] {exception}', ['exception' => $e]);
        $data['status']     = STATUS_ERROR;
        $data['message']    = $e->getCode() === 2 
            ? $e->getMessage() : 'Error while processing data! Please contact your system administrator.';

        return $data;
	}
}
