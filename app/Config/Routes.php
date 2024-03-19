<?php

namespace Config;

use App\Controllers\Employees;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

 //LOG IN
// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/login', 'LoginPage::index', ['filter' => 'notlogged']);

// Authenticate login crendentials with filter 'notlogged'
// If there's currently logged user, it will redirect to the previous 
// opposite of filter 'checkauth'
$routes->post('/authenticate', 'LoginPage::login', [
    'as'        => 'login.authenticate',
    'filter'    => 'notlogged'
]);

//LOG OUT
$routes->get('/logout',"LoginPage::logout");

//TEST
$routes->get('/test',"Test::index");

//DASHBOARD
$routes->get('/dashboard','Dashboard::index', ['filter' => 'checkauth']);
$routes->get('/', 'Dashboard::index', ['filter' => 'checkauth']);

/***************** PHASE 1 *****************/
/* HUMAN RESOURCE */
$routes->group('hr', ['filter' => 'checkauth'], static function ($routes) {
    // ACCOUNTS
    $routes->group('accounts', static function ($routes) {
        $routes->get('/', 'HR\Account::index', ['as' => 'account.home']);
        $routes->post('list', 'HR\Account::list', ['as' => 'account.list']);
        $routes->post('save', 'HR\Account::save', ['as' => 'account.save']);
        $routes->post('fetch', 'HR\Account::fetch', ['as' => 'account.fetch']);
        $routes->post('delete', 'HR\Account::delete', ['as' => 'account.delete']);
    
        // Account Profile
        $routes->get('profile','HR\AccountProfile::index', ['as' => 'account.profile']);
        $routes->post('change-password','HR\AccountProfile::changePassword', ['as' => 'account.change_pass']);
        $routes->post('change-profile-image','HR\AccountProfile::changeProfileImage', ['as' => 'account.profile.image']);
    });
    
    // EMPLOYEES
    $routes->group('employees', static function ($routes) {
        $routes->get('/', 'HR\Employee::index', ['as' => 'employee.home']);
        $routes->post('list', 'HR\Employee::list', ['as' => 'employee.list']);
        $routes->post('save', 'HR\Employee::save', ['as' => 'employee.save']);
        $routes->post('fetch', 'HR\Employee::fetch', ['as' => 'employee.fetch']);
        $routes->post('delete', 'HR\Employee::delete', ['as' => 'employee.delete']);
        $routes->post('change', 'HR\Employee::change', ['as' => 'employee.change']);
        
        // COMMON
        $routes->group('common', static function ($routes) {
            $routes->post('search-employees', 'HR\Common::searchEmployees', ['as' => 'employee.common.search']);
        });
    });
});
/* HUMAN RESOURCE */

/* SALES */
// CUSTOMERS / CLIENTS
$routes->group('clients', ['filter' => 'checkauth'], static function($routes) {
    $routes->get('/','Clients\Customer::index', ['as' => 'customer.home']);
    $routes->post('list','Clients\Customer::list', ['as' => 'customer.list']);
    $routes->post('save','Clients\Customer::save', ['as' => 'customer.save']);
    $routes->post('fetch','Clients\Customer::fetch', ['as' => 'customer.fetch']);
    $routes->post('delete','Clients\Customer::delete', ['as' => 'customer.delete']);

    // FILES
    $routes->match(['post', 'get'], 'files/(:num)','Clients\CustomerFile::fetchFiles/$1', ['as' => 'customer.files.fetch']);
    $routes->group('files', static function ($routes) {
        $routes->match(['post', 'put'], 'upload','Clients\CustomerFile::upload', ['as' => 'customer.files.upload']);
        $routes->match(['post', 'get'], 'download/(:any)','Clients\CustomerFile::download/$1', ['as' => 'customer.files.download']);
        $routes->match(['post', 'put'], 'remove','Clients\CustomerFile::remove', ['as' => 'customer.files.remove']);
    });
    
    // BRANCH
    $routes->group('branches', static function ($routes) {
        $routes->post('','Clients\CustomerBranch::list', ['as' => 'customer.branch.list']);
        $routes->post('save','Clients\CustomerBranch::save', ['as' => 'customer.branch.save']);
        $routes->post('fetch','Clients\CustomerBranch::fetch', ['as' => 'customer.branch.fetch']);
        $routes->post('delete','Clients\CustomerBranch::delete', ['as' => 'customer.branch.delete']);
    });
    
    // COMMON
    $routes->group('common', static function ($routes) {
        $routes->post('search-clients', 'Clients\Common::searchCustomers', ['as' => 'clients.common.customers']);
        $routes->post('search-client-branches', 'Clients\Common::searchCustomerBranches', ['as' => 'clients.common.customer.branches']);
    });
});

$routes->group('sales', ['filter' => 'checkauth'], static function($routes) {
    //TASK LEAD
    $routes->group('tasklead', static function($routes){
        $routes->get('/', 'Sales\TaskLead::index', ['as' => 'tasklead.home']);
        $routes->post('list', 'Sales\TaskLead::list', ['as' => 'tasklead.list']);
        $routes->post('save', 'Sales\TaskLead::save', ['as' => 'tasklead.save']);
        $routes->post('edit', 'Sales\TaskLead::edit', ['as' => 'tasklead.edit']);
        $routes->post('delete', 'Sales\TaskLead::delete', ['as' => 'tasklead.delete']);
        $routes->get('fetchcustomervt', 'Sales\TaskLead::getVtCustomer', ['as' => 'tasklead.getcustomervt']);
        $routes->get('fetchcustomerresidential', 'Sales\TaskLead::getResidentialCustomers', ['as' => 'tasklead.getcustomerresidential']);
        $routes->get('fetchcustomervtbranch', 'Sales\TaskLead::getCustomerVtBranch', ['as' => 'tasklead.getcustomervtbranch']);
        $routes->get('booked', 'Sales\TaskLeadBooked::index', ['as' => 'tasklead.booked.home']);
        $routes->post('booked/list', 'Sales\TaskLeadBooked::list', ['as' => 'tasklead.booked.list']);
        $routes->post('booked/project_details', 'Sales\TaskLeadBooked::get_booked_details', ['as' => 'tasklead.booked.details']);
        $routes->post('booked/history_details', 'Sales\TaskLeadBooked::get_tasklead_history', ['as' => 'tasklead.booked.history']);
        $routes->post('booked/upload', 'Sales\TaskLeadBooked::upload', ['as' => 'tasklead.booked.upload']);
        $routes->post('booked/tasklead_files', 'Sales\TaskLeadBooked::getTaskleadFiles', ['as' => 'tasklead.booked.files']);
        $routes->get('booked/download', 'Sales\TaskLeadBooked::downloadFile', ['as' => 'tasklead.booked.download']);
        $routes->get('booked/show/(:num)', 'Sales\TaskLeadBooked::show/$1', ['as' => 'tasklead.booked.show']);
    
        // FILES
        $routes->match(['post', 'get'], 'booked/files/(:num)','Sales\TaskLeadBookedFile::fetchFiles/$1', ['as' => 'tasklead.booked.files.fetch']);
        $routes->group('booked/files', static function ($routes) {
            $routes->match(['post', 'put'], 'upload','Sales\TaskLeadBookedFile::upload', ['as' => 'tasklead.booked.files.upload']);
            $routes->match(['post', 'get'], 'download/(:any)','Sales\TaskLeadBookedFile::download/$1', ['as' => 'tasklead.booked.files.download']);
            $routes->match(['post', 'put'], 'remove','Sales\TaskLeadBookedFile::remove', ['as' => 'tasklead.booked.files.remove']);
        });
    });

    // MANAGER
    $routes->group('manager', static function($routes){
        $routes->get('/','Sales\SalesManager::index', ['as' => 'sales_manager.home']);
        $routes->post('taskleads','Sales\SalesManager::taskleads', ['as' => 'sales_manager.taskleads']);
        $routes->post('tasklead_stats_url','Sales\SalesManager::taskleads_stats', ['as' => 'sales_manager.taskleads_stats']);
        $routes->post('tasklead_quarterly_url','Sales\SalesManager::taskleads_quarterly', ['as' => 'sales_manager.taskleads_quarterly']);
    });
    
    // MANAGER/INDIVIDUAL
    $routes->group('manager/indvidual', static function($routes){
        $routes->get('/','Sales\SalesManagerIndividual::index', ['as' => 'sales_manager_indv.home']);
        $routes->post('taskleads','Sales\SalesManagerIndividual::taskleads', ['as' => 'sales_manager_indv.taskleads']);
        $routes->post('tasklead_stats_url','Sales\SalesManagerIndividual::taskleads_stats', ['as' => 'sales_manager_indv.taskleads_stats']);
        $routes->post('tasklead_quarterly_url','Sales\SalesManagerIndividual::taskleads_quarterly', ['as' => 'sales_manager_indv.taskleads_quarterly']);
    });
    
    // TARGET
    $routes->group('target', static function($routes){
        $routes->post('save','Sales\SalesTarget::save', ['as' => 'sales_target.save']);
        $routes->post('employees','Sales\SalesTarget::employees', ['as' => 'sales_target.employees']);
        $routes->post('employee','Sales\SalesTarget::employee', ['as' => 'sales_target.employee']);
        $routes->post('list','Sales\SalesTarget::list', ['as' => 'sales_target.list']);
        $routes->post('target_sales','Sales\SalesTarget::totalSalesTarget', ['as' => 'sales_target.target_sales']);
        $routes->post('indv_sales_target','Sales\SalesTarget::indvSalesTarget', ['as' => 'sales_target.indv_sales_target']);
        $routes->post('delete','Sales\SalesTarget::delete', ['as' => 'sales_target.delete']);
    });

    // CUSTOMER SUPPORTS
    $routes->group('customer-supports', static function ($routes) {
        $routes->get('/', 'Sales\CustomerSupport::index', ['as' => 'sales.customer_support.home']);
        $routes->post('list', 'Sales\CustomerSupport::list', ['as' => 'sales.customer_support.list']);
        $routes->post('save', 'Sales\CustomerSupport::save', ['as' => 'sales.customer_support.save']);
        $routes->post('fetch', 'Sales\CustomerSupport::fetch', ['as' => 'sales.customer_support.fetch']);
        $routes->post('delete', 'Sales\CustomerSupport::delete', ['as' => 'sales.customer_support.delete']);
        $routes->post('change', 'Sales\CustomerSupport::change', ['as' => 'sales.customer_support.change']);
        $routes->get('print/(:num)', 'Sales\CustomerSupport::print/$1', ['as' => 'sales.customer_support.print']);
    });
});
/* SALES */

/* SETTINGS */
$routes->group('settings', ['filter' => 'checkauth'], static function ($routes) {
    // MAIL CONFIG
    $routes->group('mail', static function ($routes) {
        $routes->get('/','Settings\MailConfig::index', ['as' => 'mail_config.home']);
        $routes->post('save','Settings\MailConfig::save', ['as' => 'mail_config.save']);
        $routes->get('oauth2/configure','Settings\MailConfig::config', ['as' => 'mail_config.config']);
        $routes->get('oauth2/reset-token','Settings\MailConfig::reset', ['as' => 'mail_config.reset']);
    });

    // PERMISSIONS
    $routes->group('permissions', static function ($routes) {
        $routes->get('/', 'Settings\Permission::index', ['as' => 'permission.home']);
        $routes->post('list', 'Settings\Permission::list', ['as' => 'permission.list']);
        $routes->post('save', 'Settings\Permission::save', ['as' => 'permission.save']);
        $routes->post('edit', 'Settings\Permission::edit', ['as' => 'permission.edit']);
        $routes->post('delete', 'Settings\Permission::delete', ['as' => 'permission.delete']);
    });

    // ROLES
    $routes->group('roles', static function ($routes) {
        $routes->get('/', 'Settings\Roles::index', ['as' => 'roles.home']);
        $routes->post('list', 'Settings\Roles::list', ['as' => 'roles.list']);
        $routes->post('save', 'Settings\Roles::save', ['as' => 'roles.save']);
        $routes->post('edit', 'Settings\Roles::edit', ['as' => 'roles.edit']);
        $routes->post('delete', 'Settings\Roles::delete', ['as' => 'roles.delete']);
    });
    
    // GENERAL INFO
    $routes->group('general-info', static function ($routes) {
        $routes->get('/', 'Settings\GeneralInfo::index', ['as' => 'general_info.home']);
        $routes->post('save', 'Settings\GeneralInfo::save', ['as' => 'general_info.save']);
        $routes->post('upload', 'Settings\GeneralInfo::upload', ['as' => 'general_info.upload']);
        $routes->match(['get', 'post'], 'fetch', 'Settings\GeneralInfo::fetch', ['as' => 'general_info.fetch']);
    });
});

/* Access denied */
$routes->get('access-denied','Settings\Permission::denied', ['as' => 'access.denied']);
/* SETTINGS */
/***************** PHASE 1 *****************/

/***************** PHASE 2 *****************/

/* INVENTORY */
$routes->group('inventory', ['filter' => 'checkauth'], static function ($routes) {
    // Inventory
    $routes->get('/', 'Inventory\Home::index', ['as' => 'inventory.home']);
    $routes->post('list', 'Inventory\Home::list', ['as' => 'inventory.list']);
    $routes->post('save', 'Inventory\Home::save', ['as' => 'inventory.save']);
    $routes->post('edit', 'Inventory\Home::edit', ['as' => 'inventory.edit']);
    $routes->post('delete', 'Inventory\Home::delete', ['as' => 'inventory.delete']);

    // Dropdowns
    $routes->get('dropdowns', 'Inventory\Dropdown::index', ['as' => 'inventory.dropdown.home']);
    $routes->get('dropdown/types', 'Inventory\Dropdown::types', ['as' => 'inventory.dropdown.types']);
    $routes->post('dropdown/show', 'Inventory\Dropdown::show', ['as' => 'inventory.dropdown.show']);
    $routes->post('dropdown/list', 'Inventory\Dropdown::list', ['as' => 'inventory.dropdown.list']);
    $routes->post('dropdown/save', 'Inventory\Dropdown::save', ['as' => 'inventory.dropdown.save']);
    $routes->post('dropdown/edit', 'Inventory\Dropdown::edit', ['as' => 'inventory.dropdown.edit']);
    $routes->post('dropdown/delete', 'Inventory\Dropdown::delete', ['as' => 'inventory.dropdown.delete']);

    // Logs (Item In and Out)
    $routes->get('logs', 'Inventory\Logs::index', ['as' => 'inventory.logs.home']);
    $routes->post('logs/save', 'Inventory\Logs::save', ['as' => 'inventory.logs.save']);
    $routes->post('logs/list', 'Inventory\Logs::list', ['as' => 'inventory.logs.list']);

    // Common
    $routes->post('masterlist', 'Inventory\Common::searchMasterlist', ['as' => 'inventory.common.masterlist']);
    $routes->post('job-orders', 'Inventory\Common::searchJobOrders', ['as' => 'inventory.common.joborders']);

    // PROJECT REQUEST FORMS
    $routes->group('project-request-forms', static function ($routes) {
        $routes->get('/', 'Inventory\ProjectRequestForm::index', ['as' => 'prf.home']);
        $routes->post('list', 'Inventory\ProjectRequestForm::list', ['as' => 'prf.list']);
        $routes->post('save', 'Inventory\ProjectRequestForm::save', ['as' => 'prf.save']);
        $routes->post('fetch', 'Inventory\ProjectRequestForm::fetch', ['as' => 'prf.fetch']);
        $routes->post('delete', 'Inventory\ProjectRequestForm::delete', ['as' => 'prf.delete']);
        $routes->post('change', 'Inventory\ProjectRequestForm::change', ['as' => 'prf.change']);
        $routes->get('print/(:num)', 'Inventory\ProjectRequestForm::print/$1', ['as' => 'prf.print']);
    });

    // ORDER FORMS
    $routes->group('order-forms', static function ($routes) {
        $routes->get('/', 'Inventory\OrderForm::index', ['as' => 'inventory.order_form.home']);
        $routes->post('list', 'Inventory\OrderForm::list', ['as' => 'inventory.order_form.list']);
        $routes->post('save', 'Inventory\OrderForm::save', ['as' => 'inventory.order_form.save']);
        $routes->post('fetch', 'Inventory\OrderForm::fetch', ['as' => 'inventory.order_form.fetch']);
        $routes->post('delete', 'Inventory\OrderForm::delete', ['as' => 'inventory.order_form.delete']);
        $routes->post('change', 'Inventory\OrderForm::change', ['as' => 'inventory.order_form.change']);
        $routes->get('print/(:num)', 'Inventory\OrderForm::print/$1', ['as' => 'inventory.order_form.print']);
    });
});
/* INVENTORY */

/* ADMIN */
$routes->group('admin', ['filter' => 'checkauth'], static function ($routes) {
    // COMMON
    $routes->post('search-quotations', 'Admin\Common::searchQuotation', ['as' => 'admin.common.quotations']);
    $routes->post('search-schedules', 'Admin\Common::searchSchedules', ['as' => 'admin.common.schedules']);
    $routes->post('search-job-orders', 'Admin\Common::searchJobOrders', ['as' => 'admin.common.job_orders']);
    
    // JOB ORDERS
    $routes->group('job-orders', static function ($routes) {
        $routes->get('/', 'Admin\JobOrder::index', ['as' => 'admin.job_order.home']);
        $routes->post('list', 'Admin\JobOrder::list', ['as' => 'admin.job_order.list']);
        $routes->post('save', 'Admin\JobOrder::save', ['as' => 'admin.job_order.save']);
        $routes->post('fetch', 'Admin\JobOrder::fetch', ['as' => 'admin.job_order.fetch']);
        $routes->post('delete', 'Admin\JobOrder::delete', ['as' => 'admin.job_order.delete']);
        $routes->post('status', 'Admin\JobOrder::change', ['as' => 'admin.job_order.status']);
    });
    
    // SCHEDULES
    $routes->group('schedules', static function ($routes) {
        $routes->get('/', 'Admin\Schedule::index', ['as' => 'admin.schedule.home']);
        $routes->get('list', 'Admin\Schedule::list', ['as' => 'admin.schedule.list']);
        $routes->post('save', 'Admin\Schedule::save', ['as' => 'admin.schedule.save']);
        $routes->post('delete', 'Admin\Schedule::delete', ['as' => 'admin.schedule.delete']);
    });
    
    // DISPATCH
    $routes->group('dispatch', static function ($routes) {
        $routes->get('/', 'Admin\Dispatch::index', ['as' => 'admin.dispatch.home']);
        $routes->post('list', 'Admin\Dispatch::list', ['as' => 'admin.dispatch.list']);
        $routes->post('save', 'Admin\Dispatch::save', ['as' => 'admin.dispatch.save']);
        $routes->post('fetch', 'Admin\Dispatch::fetch', ['as' => 'admin.dispatch.fetch']);
        $routes->post('delete', 'Admin\Dispatch::delete', ['as' => 'admin.dispatch.delete']);
        $routes->get('print/(:num)', 'Admin\Dispatch::print/$1', ['as' => 'admin.dispatch.print']);
    });
});
/* ADMIN */

/* PURCHASING */
$routes->group('purchasing', ['filter' => 'checkauth'], static function ($routes) {
    // COMMON
    $routes->group('common', static function ($routes) {
        $routes->post('suppliers', 'Purchasing\Common::searchSuppliers', ['as' => 'purchasing.common.suppliers']);
        $routes->post('rpf', 'Purchasing\Common::searchRpf', ['as' => 'purchasing.common.rpf']);
    });

    //SUPPLIERS
    $routes->group('suppliers', static function ($routes) {
        $routes->get('/', 'Purchasing\Suppliers::index', ['as' => 'purchasing.suppliers.home']);
        $routes->post('list', 'Purchasing\Suppliers::list', ['as' => 'purchasing.suppliers.list']);
        $routes->post('save', 'Purchasing\Suppliers::save', ['as' => 'purchasing.suppliers.save']);
        $routes->post('edit', 'Purchasing\Suppliers::edit', ['as' => 'purchasing.suppliers.edit']);
        $routes->post('delete', 'Purchasing\Suppliers::delete', ['as' => 'purchasing.suppliers.delete']);
    
        $routes->group('brands', static function ($routes) {
            $routes->get('list','Purchasing\SupplierBrands::list', ['as' => 'purchasing.suppliers.brand.list']);
            $routes->post('save','Purchasing\SupplierBrands::save', ['as' => 'purchasing.suppliers.brand.save']);
            $routes->post('edit','Purchasing\SupplierBrands::edit', ['as' => 'purchasing.suppliers.brand.edit']);
            $routes->post('delete','Purchasing\SupplierBrands::delete', ['as' => 'purchasing.suppliers.brand.delete']);
        });    
    });
 
    // REQUEST TO PURCHASE FORMS
    $routes->group('request-purchase-forms', static function ($routes) {
        $routes->get('/', 'Purchasing\RequestPurchaseForm::index', ['as' => 'purchasing.rpf.home']);
        $routes->post('list', 'Purchasing\RequestPurchaseForm::list', ['as' => 'purchasing.rpf.list']);
        $routes->post('save', 'Purchasing\RequestPurchaseForm::save', ['as' => 'purchasing.rpf.save']);
        $routes->post('fetch', 'Purchasing\RequestPurchaseForm::fetch', ['as' => 'purchasing.rpf.fetch']);
        $routes->post('delete', 'Purchasing\RequestPurchaseForm::delete', ['as' => 'purchasing.rpf.delete']);
        $routes->post('change', 'Purchasing\RequestPurchaseForm::change', ['as' => 'purchasing.rpf.change']);
        $routes->get('print/(:num)', 'Purchasing\RequestPurchaseForm::print/$1', ['as' => 'purchasing.rpf.print']);
    });

    // PURCHASE ORDER / GENERATE PO
    $routes->group('purchase-orders', static function ($routes) {
        $routes->get('/', 'Purchasing\PurchaseOrder::index', ['as' => 'purchasing.purchase_order.home']);
        $routes->post('list', 'Purchasing\PurchaseOrder::list', ['as' => 'purchasing.purchase_order.list']);
        $routes->post('save', 'Purchasing\PurchaseOrder::save', ['as' => 'purchasing.purchase_order.save']);
        $routes->post('fetch', 'Purchasing\PurchaseOrder::fetch', ['as' => 'purchasing.purchase_order.fetch']);
        $routes->post('delete', 'Purchasing\PurchaseOrder::delete', ['as' => 'purchasing.purchase_order.delete']);
        $routes->post('change', 'Purchasing\PurchaseOrder::change', ['as' => 'purchasing.purchase_order.change']);
        $routes->get('print/(:num)', 'Purchasing\PurchaseOrder::print/$1', ['as' => 'purchasing.purchase_order.print']);
    });
});
/* PURCHASING */

/* REPORTS */
$routes->group('reports', ['filter' => 'checkauth'], static function ($routes) {
    // EXPORT DATA
    $routes->group('export', static function ($routes) {
        $routes->get('/', 'Reports\ExportData::index', ['as' => 'export.home']);
        $routes->post('data', 'Reports\ExportData::export', ['as' => 'export.data']);
    });
});
/* REPORTS */

/***************** PHASE 2 *****************/


/***************** PHASE 3 *****************/
/* PAYROLL */
$routes->group('hr/payroll', ['filter' => 'checkauth'], static function ($routes) {
    // SALARY RATES
    $routes->group('salary-rates', static function ($routes) {
        $routes->get('/', 'Payroll\SalaryRate::index', ['as' => 'payroll.salary_rate.home']);
        $routes->post('list', 'Payroll\SalaryRate::list', ['as' => 'payroll.salary_rate.list']);
        $routes->post('save', 'Payroll\SalaryRate::save', ['as' => 'payroll.salary_rate.save']);
        $routes->post('fetch', 'Payroll\SalaryRate::fetch', ['as' => 'payroll.salary_rate.fetch']);
        $routes->post('delete', 'Payroll\SalaryRate::delete', ['as' => 'payroll.salary_rate.delete']);
    });

    // COMPUTATION
    $routes->group('computation', static function ($routes) {
        $routes->get('/', 'Payroll\Computation::index', ['as' => 'payroll.computation.home']);
        $routes->post('save', 'Payroll\Computation::save', ['as' => 'payroll.computation.save']);
        $routes->post('govt-deductions', 'Payroll\Computation::govtDeductions', ['as' => 'payroll.computation.govt_deductions']);
    });

    // PAYSLIP
    $routes->group('payslip', static function ($routes) {
        $routes->get('/', 'Payroll\Payslip::index', ['as' => 'payroll.payslip.home']);
        $routes->post('list', 'Payroll\Payslip::list', ['as' => 'payroll.payslip.list']);
        $routes->post('save', 'Payroll\Payslip::save', ['as' => 'payroll.payslip.save']);
        $routes->post('fetch', 'Payroll\Payslip::fetch', ['as' => 'payroll.payslip.fetch']);
        $routes->post('delete', 'Payroll\Payslip::delete', ['as' => 'payroll.payslip.delete']);
        $routes->get('print/(:num)', 'Payroll\Payslip::print/$1', ['as' => 'payroll.payslip.print']);
    });

    // LEAVE
    $routes->group('leave', static function ($routes) {
        $routes->get('/', 'Payroll\Leave::index', ['as' => 'payroll.leave.home']);
        $routes->post('list', 'Payroll\Leave::list', ['as' => 'payroll.leave.list']);
        $routes->post('save', 'Payroll\Leave::save', ['as' => 'payroll.leave.save']);
        $routes->post('fetch', 'Payroll\Leave::fetch', ['as' => 'payroll.leave.fetch']);
        $routes->post('delete', 'Payroll\Leave::delete', ['as' => 'payroll.leave.delete']);
        $routes->post('change', 'Payroll\Leave::change', ['as' => 'payroll.leave.change']);
    });

    // OVERTIME
    $routes->group('overtime', static function ($routes) {
        $routes->get('/', 'Payroll\Overtime::index', ['as' => 'payroll.overtime.home']);
        $routes->post('list', 'Payroll\Overtime::list', ['as' => 'payroll.overtime.list']);
        $routes->post('save', 'Payroll\Overtime::save', ['as' => 'payroll.overtime.save']);
        $routes->post('fetch', 'Payroll\Overtime::fetch', ['as' => 'payroll.overtime.fetch']);
        $routes->post('delete', 'Payroll\Overtime::delete', ['as' => 'payroll.overtime.delete']);
        $routes->post('change', 'Payroll\Overtime::change', ['as' => 'payroll.overtime.change']);
    });

    // SETTINGS
    $routes->group('settings', static function ($routes) {
        $routes->get('/', 'Payroll\Settings::index', ['as' => 'payroll.settings.home']);
        $routes->post('save', 'Payroll\Settings::save', ['as' => 'payroll.settings.save']);
        $routes->post('fetch', 'Payroll\Settings::fetch', ['as' => 'payroll.settings.fetch']);

        // BIR TAX
        $routes->group('tax', static function ($routes) {
            $routes->post('save', 'Payroll\BirTaxSetup::save', ['as' => 'payroll.settings.tax.save']);
            $routes->post('fetch', 'Payroll\BirTaxSetup::fetch', ['as' => 'payroll.settings.tax.fetch']);
            $routes->post('delete', 'Payroll\BirTaxSetup::delete', ['as' => 'payroll.settings.tax.delete']);
        });
    });

    // TIMESHEETS
    $routes->group('timesheets', static function ($routes) {
        $routes->get('/', 'Payroll\Timesheet::index', ['as' => 'payroll.timesheet.home']);
        $routes->post('list', 'Payroll\Timesheet::list', ['as' => 'payroll.timesheet.list']);
        $routes->post('save', 'Payroll\Timesheet::save', ['as' => 'payroll.timesheet.save']);
        $routes->post('fetch', 'Payroll\Timesheet::fetch', ['as' => 'payroll.timesheet.fetch']);
        $routes->post('delete', 'Payroll\Timesheet::delete', ['as' => 'payroll.timesheet.delete']);
        $routes->post('clock', 'Payroll\Timesheet::clock', ['as' => 'payroll.timesheet.clock']);
    });
});
/* PAYROLL */

/* FINANCE */
$routes->group('finance', ['filter' => 'checkauth'], static function ($routes) {
    // BILLING INVOICE
    $routes->group('billing-invoice', static function ($routes) {
        $routes->get('/', 'Finance\BillingInvoice::index', ['as' => 'finance.billing_invoice.home']);
        $routes->post('list', 'Finance\BillingInvoice::list', ['as' => 'finance.billing_invoice.list']);
        $routes->post('save', 'Finance\BillingInvoice::save', ['as' => 'finance.billing_invoice.save']);
        $routes->post('fetch', 'Finance\BillingInvoice::fetch', ['as' => 'finance.billing_invoice.fetch']);
        $routes->post('delete', 'Finance\BillingInvoice::delete', ['as' => 'finance.billing_invoice.delete']);
        $routes->post('change', 'Finance\BillingInvoice::change', ['as' => 'finance.billing_invoice.change']);
        $routes->get('print/(:num)', 'Finance\BillingInvoice::print/$1', ['as' => 'finance.billing_invoice.print']);
    });

    // FUNDS
    $routes->group('funds', static function ($routes) {
        $routes->get('/', 'Finance\Funds::index', ['as' => 'finance.funds.home']);
        $routes->post('list', 'Finance\Funds::list', ['as' => 'finance.funds.list']);
        $routes->post('save', 'Finance\Funds::save', ['as' => 'finance.funds.save']);
        $routes->post('fetch', 'Finance\Funds::fetch', ['as' => 'finance.funds.fetch']);
    });
});
/* FINANCE */

/***************** PHASE 3 *****************/

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}