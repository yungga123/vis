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
defined('AAL_SUPERVISOR_SALES')   || define('AAL_SUPERVISOR_SALES', 'supervisor_sales');
defined('AAL_SUPERVISOR_INVENTORY')   || define('AAL_SUPERVISOR_INVENTORY', 'supervisor_inventory');
defined('AAL_SUPERVISOR_PROJECT')   || define('AAL_SUPERVISOR_PROJECT', 'supervisor_project');
defined('AAL_SUPERVISOR_PURCHASING')   || define('AAL_SUPERVISOR_PURCHASING', 'supervisor_purchasing');
defined('AAL_SUPERVISOR_HR')   || define('AAL_SUPERVISOR_HR', 'supervisor_hr');
defined('AAL_SUPERVISOR_IT')   || define('AAL_SUPERVISOR_IT', 'supervisor_it');
defined('AAL_MANAGER_TECHNICAL')   || define('AAL_MANAGER_TECHNICAL', 'manager_technical');
defined('AAL_MANAGER_ADMIN')   || define('AAL_MANAGER_ADMIN', 'manager_admin');
defined('AAL_MANAGER_SALES')   || define('AAL_MANAGER_SALES', 'manager_sales');
defined('AAL_MANAGER_HR')   || define('AAL_MANAGER_HR', 'manager_hr');
defined('AAL_MANAGER_ACCOUNTING')   || define('AAL_MANAGER_ACCOUNTING', 'manager_accounting');
defined('AAL_MANAGER_FINANCE')   || define('AAL_MANAGER_FINANCE', 'manager_finance');
defined('AAL_USER')         || define('AAL_USER', 'user');

// Actions - add new here
define('ACTIONS', [
    'VIEW'          => 'View',
    'ADD'           => 'Add',
    'EDIT'          => 'Edit',
    'DELETE'        => 'Delete',
    'OTHERS'        => [
        // Add here for specific permissions for specific module
        // Follow the format below
        // 'MODULE_NAME' => [
        //     'ACTION_NAME' => 'Action Name',
        //     'ACTION_NAME' => 'Action Name',
        //     'ACTION_NAME' => 'Action Name',
        // ],
        'INVENTORY'   => [
            'ITEM_IN'       => 'Item In',
            'ITEM_OUT'      => 'Item Out',
        ],
        'ADMIN_JOB_ORDER'   => [
            'ACCEPT'        => 'Accept',
            'DISCARD'       => 'Discard',
            'FILE'          => 'File',
            'RESCHEDULE'    => 'Reschedule',
        ],
        'ADMIN_DISPATCH'    => [
            'PRINT'         => 'Print',
        ],
        'INVENTORY_PRF'     => [
            'ACCEPT'        => 'Accept',
            'REJECT'        => 'Reject',
            'ITEM_OUT'      => 'Item Out',
            'FILE'          => 'File',
            'PRINT'         => 'Print',
        ],
        'PURCHASING_RPF'    => [
            'ACCEPT'        => 'Accept',
            'REJECT'        => 'Reject',
            'RECEIVE'       => 'Receive',
            'PRINT'         => 'Print',
        ],
        'PURCHASING_PO'     => [
            'APPROVE'       => 'Approve',
            'FILE'          => 'File',
            'PRINT'         => 'Print',
        ],
        'CUSTOMERS'         => [
            'UPLOAD'        => 'Upload',
        ],
        'EMPLOYEES'         => [
            'CHANGE'        => 'Change Employment Status',
        ],
    ],
]);

// Individual actions constants
define('ACTION_ADD', 'ADD');
define('ACTION_EDIT', 'EDIT');
define('ACTION_DELETE', 'DELETE');
define('ACTION_PRINT', 'PRINT');
define('ACTION_UPLOAD', 'UPLOAD');
define('ACTION_CHANGE', 'CHANGE');

// Roles - No need to add new roles here
// Adding new roles will be on the dashboard
define('ROLES', [
    'ADMIN'                 => 'Administrator',
    'EXECUTIVE'             => 'Executive',
    'MANAGER'               => 'Manager',
    'MANAGER_TECHNICAL'     => 'Technical Manager',
    'MANAGER_ADMIN'         => 'Admin Manager',
    'MANAGER_SALES'         => 'Sales Manager',
    'MANAGER_HR'            => 'HR Manager',
    'MANAGER_ACCOUNTING'    => 'Accounting Manager',
    'MANAGER_FINANCE'       => 'Finance Manager',
    'OPERATION'             => 'Admin/Operation',
    'SUPERVISOR'            => 'Supervisory',
    'SUPERVISOR_SALES'      => 'Sales Supervisor',
    'SUPERVISOR_INVENTORY'  => 'Inventory',
    'SUPERVISOR_PROJECT'    => 'Project Engineer',
    'SUPERVISOR_PURCHASING' => 'Purchasing',
    'SUPERVISOR_HR'         => 'HR Staff',
    'SUPERVISOR_IT'         => 'IT Head',
    'USER'                  => 'General User',
]);

// Modules - add new here
define('MODULES', [
    'DASHBOARD'             => 'Dashboard',
    'ACCOUNTS'              => 'Accounts',
    'EMPLOYEES'             => 'Employees',
    'CUSTOMERS'             => 'Clients',
    'TASK_LEAD'             => 'Task/Lead Monitoring',
    'MANAGER_OF_SALES'      => 'Manager of Sales',
    'MANAGER_OF_SALES_INDV' => 'Manager of Sales (Individual)',
    'INVENTORY'             => 'Items Masterlist',
    'SETTINGS_MAILCONFIG'   => 'Mail Config',
    'SETTINGS_PERMISSIONS'  => 'Permissions',
    'SETTINGS_ROLES'        => 'Roles',
    'PURCHASING_SUPPLIERS'  => 'Suppliers',
    'ADMIN_JOB_ORDER'       => 'Job Orders',
    'ADMIN_SCHEDULES'       => 'Schedules',
    'ADMIN_DISPATCH'        => 'Dispatch',
    'INVENTORY_PRF'         => 'Project Request Forms (PRF)',
    'PURCHASING_RPF'        => 'Request to Purchase Forms (RPF)',
    'PURCHASING_PO'         => 'Purchase Orders',
    'SETTINGS_GENERAL_INFO' => 'General Info',
    'EXPORT_DATA'           => 'Export Data',
    'PAYROLL_SALARY_RATES'  => 'Salary Rates',
    'PAYROLL_PAYSLIP'       => 'Payslip',
    'PAYROLL_COMPUTATION'   => 'Computation',
]);

// Modules code based on the specific identifier you set
// (value should be the same key in MODULES)
// Will be used in the constructor of the controller
define('MODULE_CODES', [
    'dashboard'             => 'DASHBOARD',
    'accounts'              => 'ACCOUNTS',
    'employees'             => 'EMPLOYEES',
    'customers'             => 'CUSTOMERS',
    'task_lead'             => 'TASK_LEAD',
    'manager_sales'         => 'MANAGER_OF_SALES',
    'manager_sales_indv'    => 'MANAGER_OF_SALES_INDV',
    'inventory'             => 'INVENTORY',
    'mail_config'           => 'SETTINGS_MAILCONFIG',
    'permissions'           => 'SETTINGS_PERMISSIONS',
    'roles'                 => 'SETTINGS_ROLES',
    'manager_sales_indv'    => 'MANAGER_OF_SALES_INDV',
    'suppliers'             => 'PURCHASING_SUPPLIERS',
    'job_orders'            => 'ADMIN_JOB_ORDER',
    'schedules'             => 'ADMIN_SCHEDULES',
    'dispatch'              => 'ADMIN_DISPATCH',
    'inventory_prf'         => 'INVENTORY_PRF',
    'purchasing_rpf'        => 'PURCHASING_RPF',
    'purchase_order'        => 'PURCHASING_PO',
    'general_info'          => 'SETTINGS_GENERAL_INFO',
    'export_data'           => 'EXPORT_DATA',
    'salary_rates'          => 'PAYROLL_SALARY_RATES',
    'payslip'               => 'PAYROLL_PAYSLIP',
    'payroll_computation'   => 'PAYROLL_COMPUTATION',
]);

// Developer Account
define('DEVELOPER_ACCOUNT', 'SOFTWAREDEV');
define('DEVELOPER_USERNAME', 'yungga');

// Company Info 
// - These following constants will be used as default data
// - if there's no entered data in General Info module
define('COMPANY_NAME', 'Vinculum Technologies Corporation');
define('COMPANY_ADDRESS', '#70 National Road., Putatan, Muntinlupa City');
define('COMPANY_CONTACT_NUMBER', '');
define('COMPANY_EMAIL', '');
// Purchase Order Form Code
define('COMPANY_PO_FORM_CODE', 'F06');

// Root path directory for all file uploads
// Para isahan nlng ng directory
// Don't foget to add '/' at the end
define('ROOT_FILE_UPLOAD_DIR', '');