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
    protected $helpers = ['url', 'form', 'formatter'];


    /**
     * Add custom properties accessible throughout all controllers
     */
    // Will be use to initialize \Config\Database::connect();
    protected $builder;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();

        $this->builder = \Config\Database::connect();
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
     * For sending mail to employee
     * $request param should contain [employee_id, username, password]
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

        $params['email_address'] = 'radyballs69@gmail.com';
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
}
