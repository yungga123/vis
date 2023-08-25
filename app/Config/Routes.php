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
//ACCOUNTS
$routes->get('accounts','Accounts::index', ['filter' => 'checkauth', 'as' => 'account.home']);
$routes->group('account', ['filter' => 'checkauth'], static function ($routes) {
    $routes->post('list', 'Accounts::list', ['as' => 'account.list']);
    $routes->post('save', 'Accounts::save', ['as' => 'account.save']);
    $routes->post('edit', 'Accounts::edit', ['as' => 'account.edit']);
    $routes->post('delete', 'Accounts::delete', ['as' => 'account.delete']);

    // Account Profile
    $routes->get('profile','AccountProfile::index', ['as' => 'account.profile']);
    $routes->post('change-password','AccountProfile::changePassword', ['as' => 'account.change_pass']);
    $routes->post('change-profile-image','AccountProfile::changeProfileImage', ['as' => 'account.profile.image']);
});

//EMPLOYEES
$routes->get('employees','Employees::index', ['filter' => 'checkauth', 'as' => 'employee.home']);
$routes->group('employee', ['filter' => 'checkauth'], static function ($routes) {
    $routes->post('list', 'Employees::list', ['as' => 'employee.list']);
    $routes->post('save', 'Employees::save', ['as' => 'employee.save']);
    $routes->post('edit', 'Employees::edit', ['as' => 'employee.edit']);
    $routes->post('delete', 'Employees::delete', ['as' => 'employee.delete']);
});
/* HUMAN RESOURCE */

/* SALES */
// CUSTOMERS - FORECAST
// $routes->group('customers',['filter' => 'checkauth'], static function($routes) {
//     $routes->get('/','Customers::index', ['as' => 'customers.home']);
//     $routes->post('list','Customers::list',['as' => 'customers.list']);
//     $routes->post('save','Customers::save',['as' => 'customers.save']);
//     $routes->post('edit','Customers::edit',['as' => 'customers.edit']);
//     $routes->post('delete','Customers::delete',['as' => 'customers.delete']);
//     $routes->get('branch','Customers::branchCustomersList',['as' => 'customers.branchlist']);
//     $routes->post('customerget','Customers::getCustomers',['as' => 'customersbranch.getcustomer']);
//     $routes->post('saveBranch','Customers::saveBranch',['as' => 'customersbranch.save']);
//     $routes->post('editBranch','Customers::editBranch',['as' => 'customersbranch.edit']);
//     $routes->post('deleteBranch','Customers::deleteBranch',['as' => 'customersbranch.delete']);
// });

// CUSTOMERS VT 
$routes->group('customers/commercial',['filter' => 'checkauth'],static function($routes){
    $routes->get('/','CustomersVt::index', ['as' => 'customervt.home']);
    $routes->post('list','CustomersVt::list',['as' => 'customervt.list']);
    $routes->get('list','CustomersVt::list',['as' => 'customervt.listget']);
    $routes->post('save','CustomersVt::save',['as' => 'customervt.save']);
    $routes->post('edit','CustomersVt::edit',['as' => 'customervt.edit']);
    $routes->post('delete','CustomersVt::delete',['as' => 'customervt.delete']);
    $routes->get('branch','CustomersVt::branchCustomervtList',['as' => 'customervt.branchlist']);
    $routes->post('customerget','CustomersVt::getCustomers',['as' => 'customervtbranch.getcustomer']);
    $routes->post('saveBranch','CustomersVt::saveBranch',['as' => 'customervtbranch.save']);
    $routes->post('editBranch','CustomersVt::editBranch',['as' => 'customervtbranch.edit']);
    $routes->post('deleteBranch','CustomersVt::deleteBranch',['as' => 'customervtbranch.delete']);
});

// CUSTOMERS RESIDENTIAL
$routes->group('customers/residential',['filter' => 'checkauth'],static function($routes){
    $routes->get('/','CustomersResidential::index', ['as' => 'customersresidential.home']);
    $routes->post('list','CustomersResidential::list',['as' => 'customersresidential.list']);
    $routes->get('list','CustomersResidential::list',['as' => 'customersresidential.listget']);
    $routes->post('save','CustomersResidential::save',['as' => 'customersresidential.save']);
    $routes->post('edit','CustomersResidential::edit',['as' => 'customersresidential.edit']);
    $routes->post('delete','CustomersResidential::delete',['as' => 'customersresidential.delete']);
});

//Task Lead
$routes->group('tasklead', ['filter' => 'checkauth'], static function($routes){
    $routes->get('/','TaskLead::index', ['as' => 'tasklead.home']);
    $routes->post('list','TaskLead::list',['as' => 'tasklead.list']);
    $routes->post('save','TaskLead::save',['as' => 'tasklead.save']);
    $routes->post('edit','TaskLead::edit',['as' => 'tasklead.edit']);
    $routes->post('delete','TaskLead::delete',['as' => 'tasklead.delete']);
    $routes->get('fetchcustomervt','TaskLead::getVtCustomer',['as' => 'tasklead.getcustomervt']);
    $routes->get('fetchcustomerresidential','TaskLead::getResidentialCustomers',['as' => 'tasklead.getcustomerresidential']);
    $routes->get('fetchcustomervtbranch','TaskLead::getCustomerVtBranch',['as' => 'tasklead.getcustomervtbranch']);
    $routes->get('booked','TaskLeadBooked::index', ['as' => 'tasklead.booked.home']);
    $routes->post('booked/list','TaskLeadBooked::list', ['as' => 'tasklead.booked.list']);
    $routes->post('booked/project_details','TaskLeadBooked::get_booked_details',['as' => 'tasklead.booked.details']);
    $routes->post('booked/history_details','TaskLeadBooked::get_tasklead_history',['as' => 'tasklead.booked.history']);
    $routes->post('booked/upload','TaskLeadBooked::upload',['as' => 'tasklead.booked.upload']);
    $routes->post('booked/tasklead_files','TaskLeadBooked::getTaskleadFiles',['as' => 'tasklead.booked.files']);
    $routes->get('booked/download','TaskLeadBooked::downloadFile',['as' => 'tasklead.booked.download']);
    $routes->get('booked/show/(:num)','TaskLeadBooked::show', ['as' => 'tasklead.booked.show/$1']);
});

// Sales Manager
$routes->group('sales_manager', ['filter' => 'checkauth'], static function($routes){
    $routes->get('/','SalesManager::index',['as' => 'sales_manager.home']);
    $routes->post('taskleads','SalesManager::taskleads',['as' => 'sales_manager.taskleads']);
    $routes->post('tasklead_stats_url','SalesManager::taskleads_stats',['as' => 'sales_manager.taskleads_stats']);
    $routes->post('tasklead_quarterly_url','SalesManager::taskleads_quarterly',['as' => 'sales_manager.taskleads_quarterly']);
});

// Sales Manager Individual
$routes->group('sales_manager_indv', ['filter' => 'checkauth'], static function($routes){
    $routes->get('/','SalesManagerIndividual::index',['as' => 'sales_manager_indv.home']);
    $routes->post('taskleads','SalesManagerIndividual::taskleads',['as' => 'sales_manager_indv.taskleads']);
    $routes->post('tasklead_stats_url','SalesManagerIndividual::taskleads_stats',['as' => 'sales_manager_indv.taskleads_stats']);
    $routes->post('tasklead_quarterly_url','SalesManagerIndividual::taskleads_quarterly',['as' => 'sales_manager_indv.taskleads_quarterly']);
});

// Sales Target
$routes->group('sales_target', ['filter' => 'checkauth'], static function($routes){
    $routes->post('save','SalesTarget::save',['as' => 'sales_target.save']);
    $routes->post('employees','SalesTarget::employees',['as' => 'sales_target.employees']);
    $routes->post('employee','SalesTarget::employee',['as' => 'sales_target.employee']);
    $routes->post('list','SalesTarget::list',['as' => 'sales_target.list']);
    $routes->post('target_sales','SalesTarget::totalSalesTarget',['as' => 'sales_target.target_sales']);
    $routes->post('indv_sales_target','SalesTarget::indvSalesTarget',['as' => 'sales_target.indv_sales_target']);
    $routes->post('delete','SalesTarget::delete',['as' => 'sales_target.delete']);
});
/* SALES */

/* SETTINGS */
// MAIL CONFIG
$routes->group('settings/mail', ['filter' => 'checkauth'], static function ($routes) {
    $routes->get('/','Settings\MailConfig::index', ['as' => 'mail.home']);
    $routes->post('save','Settings\MailConfig::save', ['as' => 'mail.save']);
    $routes->get('oauth2/configure','Settings\MailConfig::config', ['as' => 'mail.config']);
    $routes->get('oauth2/reset-token','Settings\MailConfig::reset', ['as' => 'mail.reset']);
});

//PERMISSIONS
$routes->group('settings/permissions', ['filter' => 'checkauth'], static function ($routes) {
    $routes->get('/', 'Settings\Permission::index', ['as' => 'permission.home']);
    $routes->post('list', 'Settings\Permission::list', ['as' => 'permission.list']);
    $routes->post('save', 'Settings\Permission::save', ['as' => 'permission.save']);
    $routes->post('edit', 'Settings\Permission::edit', ['as' => 'permission.edit']);
    $routes->post('delete', 'Settings\Permission::delete', ['as' => 'permission.delete']);
});

//ROLES
$routes->group('settings/roles', ['filter' => 'checkauth'], static function ($routes) {
    $routes->get('/', 'Settings\Roles::index', ['as' => 'roles.home']);
    $routes->post('list', 'Settings\Roles::list', ['as' => 'roles.list']);
    $routes->post('save', 'Settings\Roles::save', ['as' => 'roles.save']);
    $routes->post('edit', 'Settings\Roles::edit', ['as' => 'roles.edit']);
    $routes->post('delete', 'Settings\Roles::delete', ['as' => 'roles.delete']);
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
    $routes->get('dropdowns', 'Inventory\\Dropdown::index', ['as' => 'inventory.dropdown.home']);
    $routes->get('dropdown/types', 'Inventory\\Dropdown::types', ['as' => 'inventory.dropdown.types']);
    $routes->post('dropdown/show', 'Inventory\\Dropdown::show', ['as' => 'inventory.dropdown.show']);
    $routes->post('dropdown/list', 'Inventory\\Dropdown::list', ['as' => 'inventory.dropdown.list']);
    $routes->post('dropdown/save', 'Inventory\\Dropdown::save', ['as' => 'inventory.dropdown.save']);
    $routes->post('dropdown/edit', 'Inventory\\Dropdown::edit', ['as' => 'inventory.dropdown.edit']);
    $routes->post('dropdown/delete', 'Inventory\\Dropdown::delete', ['as' => 'inventory.dropdown.delete']);

    // Logs (Item In and Out)
    $routes->get('logs', 'InventoryLogs::index', ['as' => 'inventory.logs.home']);
    $routes->post('logs/save', 'InventoryLogs::save', ['as' => 'inventory.logs.save']);
    $routes->post('logs/list', 'InventoryLogs::list', ['as' => 'inventory.logs.list']);
});

/* ADMIN */
// Common
$routes->group('admin', ['filter' => 'checkauth'], static function ($routes) {
    $routes->post('quotations', 'Admin\Common::searchQuotation', ['as' => 'admin.common.quotations']);
    $routes->post('schedules', 'Admin\Common::searchSchedules', ['as' => 'admin.common.schedules']);
    $routes->post('customers', 'Admin\Common::searchCustomers', ['as' => 'admin.common.customers']);
    $routes->post('schedules', 'Admin\Common::searchSchedules', ['as' => 'admin.common.schedules']);
    $routes->post('customers', 'Admin\Common::searchCustomers', ['as' => 'admin.common.customers']);
});
    
// JOB ORDERS
$routes->group('job-orders', ['filter' => 'checkauth'], static function ($routes) {
    $routes->get('/', 'Admin\JobOrder::index', ['as' => 'job_order.home']);
    $routes->post('list', 'Admin\JobOrder::list', ['as' => 'job_order.list']);
    $routes->post('save', 'Admin\JobOrder::save', ['as' => 'job_order.save']);
    $routes->post('fetch', 'Admin\JobOrder::fetch', ['as' => 'job_order.fetch']);
    $routes->post('delete', 'Admin\JobOrder::delete', ['as' => 'job_order.delete']);
    $routes->post('status', 'Admin\JobOrder::change', ['as' => 'job_order.status']);
});

// SCHEDULES
$routes->group('schedules', ['filter' => 'checkauth'], static function ($routes) {
    $routes->get('/', 'Admin\Schedule::index', ['as' => 'schedule.home']);
    $routes->get('list', 'Admin\Schedule::list', ['as' => 'schedule.list']);
    $routes->post('save', 'Admin\Schedule::save', ['as' => 'schedule.save']);
    $routes->post('delete', 'Admin\Schedule::delete', ['as' => 'schedule.delete']);
});

// DISPATCH
$routes->group('dispatch', ['filter' => 'checkauth'], static function ($routes) {
    $routes->get('/', 'Admin\Dispatch::index', ['as' => 'dispatch.home']);
    $routes->post('list', 'Admin\Dispatch::list', ['as' => 'dispatch.list']);
    $routes->post('save', 'Admin\Dispatch::save', ['as' => 'dispatch.save']);
    $routes->post('fetch', 'Admin\Dispatch::fetch', ['as' => 'dispatch.fetch']);
    $routes->post('delete', 'Admin\Dispatch::delete', ['as' => 'dispatch.delete']);
    $routes->get('print/(:num)', 'Admin\Dispatch::print', ['as' => 'dispatch.print/$1']);
});
/* ADMIN */


/* PURCHASING */
// Common
$routes->group('purchasing', ['filter' => 'checkauth'], static function ($routes) {
    //
});

//SUPPLIERS
$routes->group('suppliers', ['filter' => 'checkauth'], static function ($routes) {
    $routes->get('/', 'Suppliers::index', ['as' => 'suppliers.home']);
    $routes->post('list', 'Suppliers::list', ['as' => 'suppliers.list']);
    $routes->post('save', 'Suppliers::save', ['as' => 'suppliers.save']);
    $routes->post('edit', 'Suppliers::edit', ['as' => 'suppliers.edit']);
    $routes->post('delete', 'Suppliers::delete', ['as' => 'suppliers.delete']);

    $routes->get('brands/list','SupplierBrands::list',['as' => 'suppliers.brand.list']);
    $routes->post('brands/save','SupplierBrands::save',['as' => 'suppliers.brand.save']);
    $routes->post('brands/edit','SupplierBrands::edit',['as' => 'suppliers.brand.edit']);
    $routes->post('brands/delete','SupplierBrands::delete',['as' => 'suppliers.brand.delete']);

});

// REQUEST TO PURCHASE FORMS
$routes->group('', ['filter' => 'checkauth'], static function ($routes) {
    $routes->get('request-purchase-forms', 'Purchasing\RequestPurchaseForm::index', ['as' => 'rpf.home']);
    $routes->post('rpf/list', 'Purchasing\RequestPurchaseForm::list', ['as' => 'rpf.list']);
    $routes->post('rpf/save', 'Purchasing\RequestPurchaseForm::save', ['as' => 'rpf.save']);
    $routes->post('rpf/fetch', 'Purchasing\RequestPurchaseForm::fetch', ['as' => 'rpf.fetch']);
    $routes->post('rpf/delete', 'Purchasing\RequestPurchaseForm::delete', ['as' => 'rpf.delete']);
    $routes->post('rpf/change', 'Purchasing\RequestPurchaseForm::change', ['as' => 'rpf.change']);
    $routes->get('rpf/print/(:num)', 'Purchasing\RequestPurchaseForm::print', ['as' => 'rpf.print/$1']);
});
/* PURCHASING */


/***************** PHASE 2 *****************/

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