<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
 |
 | This defines the default Namespace that is used throughout
 | CodeIgniter to refer to the Application directory. Change
 | this constant to change the namespace that all application
 | classes should use.
 |
 | NOTE: changing this will require manually modifying the
 | existing namespaces of App\* namespaced-classes.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | The path that Composer's autoload file is expected to live. By default,
 | the vendor folder is in the Root directory, but you can customize that here.
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Provide simple ways to work with the myriad of PHP functions that
 | require information to be in seconds.
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2_592_000);
defined('YEAR')   || define('YEAR', 31_536_000);
defined('DECADE') || define('DECADE', 315_360_000);

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
 |
 | Used to indicate the conditions under which the script is exit()ing.
 | While there is no universal standard for error codes, there are some
 | broad conventions.  Three such conventions are mentioned below, for
 | those who wish to make use of them.  The CodeIgniter defaults were
 | chosen for the least overlap with these conventions, while still
 | leaving room for others to be defined in future versions and user
 | applications.
 |
 | The three main conventions used for determining exit status codes
 | are as follows:
 |
 |    Standard C/C++ Library (stdlibc):
 |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
 |       (This link also contains other GNU-specific conventions)
 |    BSD sysexits.h:
 |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
 |    Bash scripting:
 |       http://tldp.org/LDP/abs/html/exitcodes.html
 |
 */
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0);        // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1);          // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3);         // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4);   // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5);  // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7);     // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8);       // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9);      // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125);    // highest automatically-assigned error code

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_LOW instead.
 */
define('EVENT_PRIORITY_LOW', 200);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_NORMAL instead.
 */
define('EVENT_PRIORITY_NORMAL', 100);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_HIGH instead.
 */
define('EVENT_PRIORITY_HIGH', 10);

/* Custom constant */
// For status
define('STATUS_SUCCESS', 'success');
define('STATUS_ERROR', 'error');
define('STATUS_INFO', 'info');

/* Account Access Level (AAL) */
// New
defined('AAL_SUPER_ADMIN')  || define('AAL_SUPER_ADMIN', 'super_admin');
defined('AAL_ADMIN')        || define('AAL_ADMIN', 'admin');
defined('AAL_EXECUTIVE')    || define('AAL_EXECUTIVE', 'executive');
defined('AAL_MANAGER')      || define('AAL_MANAGER', 'manager');
defined('AAL_OPERATION')    || define('AAL_OPERATION', 'operation');
defined('AAL_SUPERVISOR')   || define('AAL_SUPERVISOR', 'supervisor');
// defined('AAL_USER')         || define('AAL_USER', 'user');

// Old
// defined('AAL_ADMIN')        || define('AAL_ADMIN', 'admin');
defined('AAL_MANAGER')      || define('AAL_MANAGER', 'manager');
defined('AAL_HR')           || define('AAL_HR', 'hr');
defined('AAL_SALES')        || define('AAL_SALES', 'sales');
defined('AAL_OFCADMIN')     || define('AAL_OFCADMIN', 'ofcadmin');
defined('AAL_USER')         || define('AAL_USER', 'user');

// Actions - add new here
define('ACTIONS', [
    'VIEW'      => 'View',
    'ADD'       => 'Add',
    'EDIT'      => 'Edit',
    'DELETE'    => 'Delete',
    // 'EXPORT'    => 'Export',
    // 'IMPORT'    => 'Import',
]);

// Roles - add new here
define('ROLES', [
    'ADMIN'         => 'Administrator',
    'EXECUTIVE'     => 'Executive',
    'MANAGER'       => 'Manager',
    'OPERATION'     => 'Admin/Operation',
    'SUPERVISOR'    => 'Supervisory',
    'USER'          => 'General User',
]);

// Modules - add new here
define('MODULES', [
    'DASHBOARD'             => 'Dashboard',
    'ACCOUNTS'              => 'Accounts',
    'EMPLOYEES'             => 'Employees',
    'CUSTOMERS'             => 'Customers',
    'CUSTOMERS_BRANCH'      => 'Customers Branch',
    'TASK_LEAD'             => 'Task/Lead Monitoring',
    'MANAGER_OF_SALES'      => 'Manager of Sales',
    'SETTINGS_MAILCONFIG'   => 'Settings | Mail Configuration',
    'SETTINGS_PERMISSIONS'  => 'Settings | Permissions',
]);

// Modules code based on the specific identifier you set
// (value should be the same key in MODULES)
// Will be used in the constructor of the controller
define('MODULE_CODES', [
    'dashboard'             => 'DASHBOARD',
    'accounts'              => 'ACCOUNTS',
    'employees'             => 'EMPLOYEES',
    'customers'             => 'CUSTOMERS',
    'c_barnch'              => 'CUSTOMERS_BRANCH',
    'task_lead'             => 'TASK_LEAD',
    'sales'                 => 'MANAGER_OF_SALES',
    'mail_config'           => 'SETTINGS_MAILCONFIG',
    'permissions'           => 'SETTINGS_PERMISSIONS',
]);

// Modules code based on the uri of the modules
// (value should be the same key in MODULES)
define('MODULE_CODES_URI', [
    'dashboard'             => 'DASHBOARD',
    'accounts'              => 'ACCOUNTS',
    'employees'             => 'EMPLOYEES',
    'customers'             => 'CUSTOMERS',
    'c_barnch'              => 'CUSTOMERS_BRANCH',
    'task_lead'             => 'TASK_LEAD',
    'sales'                 => 'MANAGER_OF_SALES',
    'settings/mail_config'  => 'SETTINGS_MAILCONFIG',
    'settings/permissions'  => 'SETTINGS_PERMISSIONS',
]);