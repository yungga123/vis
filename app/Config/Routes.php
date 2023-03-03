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

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/login', 'LoginPage::index', ['filter' => 'notlogged']);
// $routes->get('/login_validate', 'LoginPage::sign_in', ['filter' => 'notlogged']);
// $routes->post('/login_validate', 'LoginPage::sign_in', ['filter' => 'notlogged']);

// Authenticate login crendentials with filter 'notlogged'
// If there's currently logged user, it will redirect to the previous 
// opposite of filter 'checkauth'
$routes->post('/authenticate', 'LoginPage::login', [
    'as'        => 'login.authenticate',
    'filter'    => 'notlogged'
]);

//TEST
$routes->get('/test',"Test::index");

//LOG OUT
$routes->get('/logout',"LoginPage::logout");

//DASHBOARD ROUTE
$routes->get('/dashboard','Dashboard::index', ['filter' => 'checkauth']);
$routes->get('/', 'Dashboard::index', ['filter' => 'checkauth']);

//SALES DASHBOARD
// $routes->get('/sales-dashboard','SalesDashboard::index');

//ADMIN DASHBOARD
// $routes->get('/admin-dashboard','AdminDashboard::index');

//EXECUTIVE OVERVIEW
// $routes->get('/executive-overview','ExecutiveOverview::index');

// CUSTOMERS RECONSTRUCTED - FORECAST
$routes->group('customers',['filter' => 'checkauth'],static function($routes){
    $routes->get('/','Customers::index', ['as' => 'customers.home']);
    $routes->post('list','Customers::list',['as' => 'customers.list']);
    $routes->post('save','Customers::save',['as' => 'customers.save']);
    $routes->post('edit','Customers::edit',['as' => 'customers.edit']);
    $routes->post('delete','Customers::delete',['as' => 'customers.delete']);
    $routes->get('branch','Customers::branchCustomersList',['as' => 'customers.branchlist']);
    $routes->post('customerget','Customers::getCustomers',['as' => 'customersbranch.getcustomer']);
    $routes->post('saveBranch','Customers::saveBranch',['as' => 'customersbranch.save']);
    $routes->post('editBranch','Customers::editBranch',['as' => 'customersbranch.edit']);
    $routes->post('deleteBranch','Customers::deleteBranch',['as' => 'customersbranch.delete']);
});

// CUSTOMERS VT RECONSTRUCTED
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



//CUSTOMERS BRANCH
$routes->get('/add-customer-branch','CustomerBranch::index');
$routes->post('/add-customerbranch','CustomerBranch::add_customer_validate');
$routes->get('/edit-customerbranch/(:num)','CustomerBranch::edit_customer_branch/$1');
$routes->post('/edit-customerbranch/(:num)','CustomerBranch::edit_customer_branch/$1');
$routes->get('/delete-customer-branch/(:num)','CustomerBranch::delete_customer_branch/$1');

//TaskLead old
// $routes->get('/tasklead','TaskLead::index');
// $routes->get('/tasklead-addproject','TaskLead::add_project');
// $routes->get('/tasklead-editproject/(:num)','TaskLead::edit_project/$1');
// $routes->post('/post-addproject','TaskLead::add_project_validate');
// $routes->post('/post-editproject','TaskLead::edit_project_validate');
// $routes->get('/project-list','TaskLead::project_list');
// $routes->get('/manager-project-list','TaskLead::manager_project_list');
// $routes->get('/project-list-booked','TaskLead::project_list_booked');
// $routes->get('/manager-project-list-booked','TaskLead::manager_project_list_booked');
// $routes->get('/project-table-booked','TaskLead::getProjectBookedList');
// $routes->get('/manager-project-table-booked','TaskLead::getProjectListBookedManager');
// $routes->get('/project-table','TaskLead::getProjectList');
// $routes->get('/manager-project-table','TaskLead::getProjectListManager');
// $routes->get('/delete-tasklead/(:num)','Tasklead::delete_tasklead/$1');
// $routes->get('/update-tasklead/(:num)/(:any)','Tasklead::update_project_status/$1/$2');
// $routes->get('/booked-status/(:num)','TaskLead::booked_status/$1');
// $routes->post('/post-booked-status','TaskLead::booked_status_validate');
// $routes->get('/project-booked-details/(:num)','TaskLead::project_booked_details/$1');
// $routes->post('/post-tasklead-upload/(:num)','Tasklead::upload/$1');
// $routes->get('/add-project','TaskLead::add_identified');
// $routes->post('/add-project','TaskLead::add_identified');
// $routes->post('post-update-project-status','TaskLead::update_project_status_validate');
// $routes->get('/add-project-existingcustomer','TaskLead::add_projectExistingCustomer');
// $routes->post('/add-project-existingcustomer','TaskLead::add_projectExistingCustomer');

//Task Lead Reconstructed
$routes->group('tasklead', ['filter' => 'checkauth'], static function($routes){
    $routes->get('/','Tasklead::index', ['as' => 'tasklead.home']);
    $routes->get('list','Tasklead::list',['as' => 'tasklead.list']);
    $routes->post('save','Tasklead::save',['as' => 'tasklead.save']);
    $routes->post('edit','Tasklead::edit',['as' => 'tasklead.edit']);
    $routes->post('delete','Tasklead::delete',['as' => 'tasklead.delete']);
    $routes->get('fetchcustomervt','Tasklead::getVtCustomer',['as' => 'tasklead.getcustomervt']);
    // $routes->post('fetchcustomerforecast','Tasklead::getForecastCustomer',['as' => 'tasklead.getforecastcustomer']);
    $routes->get('fetchcustomerresidential','TaskLead::getResidentialCustomers',['as' => 'tasklead.getcustomerresidential']);
    $routes->get('fetchcustomervtbranch','Tasklead::getCustomerVtBranch',['as' => 'tasklead.getcustomervtbranch']);
    $routes->get('booked','TaskLeadBooked::index', ['as' => 'tasklead.booked.home']);
    $routes->post('booked/list','TaskLeadBooked::list', ['as' => 'tasklead.booked.list']);
});

//SALES MANAGER
$routes->get('/manager-of-sales','SalesManager::index');
$routes->get('/consolidated-sales-forecast','SalesManager::consolidated_forecast');

//EMPLOYEES
$routes->get('employees','Employees::index', ['filter' => 'checkauth', 'as' => 'employee.home']);
$routes->group('employee', ['filter' => 'checkauth'], static function ($routes) {
    $routes->post('list', 'Employees::list', ['as' => 'employee.list']);
    $routes->post('save', 'Employees::save', ['as' => 'employee.save']);
    $routes->post('edit', 'Employees::edit', ['as' => 'employee.edit']);
    $routes->post('delete', 'Employees::delete', ['as' => 'employee.delete']);
});

//ACCOUNTS
$routes->get('accounts','Accounts::index', ['filter' => 'checkauth', 'as' => 'account.home']);
$routes->group('account', ['filter' => 'checkauth'], static function ($routes) {
    $routes->post('list', 'Accounts::list', ['as' => 'account.list']);
    $routes->post('save', 'Accounts::save', ['as' => 'account.save']);
    $routes->post('edit', 'Accounts::edit', ['as' => 'account.edit']);
    $routes->post('delete', 'Accounts::delete', ['as' => 'account.delete']);

    // Account Profile
    $routes->get('profile','AccountProfile::index', ['as' => 'account.profile']);
    $routes->post('change-password','AccountProfile::change_password', ['as' => 'account.change_pass']);
});

//INVENTORY
$routes->group('inventory', ['filter' => 'checkauth'], static function ($routes) {
    $routes->get('/', 'Inventory::index', ['as' => 'inventory.home']);
    $routes->post('list', 'Inventory::list', ['as' => 'inventory.list']);
    $routes->post('save', 'Inventory::save', ['as' => 'inventory.save']);
    $routes->post('edit', 'Inventory::edit', ['as' => 'inventory.edit']);
    $routes->post('delete', 'Inventory::delete', ['as' => 'inventory.delete']);
});

// SETTINGS
/* Mail Config */
$routes->group('settings/mail', ['filter' => 'checkauth'], static function ($routes) {
    $routes->get('/','Settings\MailConfig::index', ['as' => 'mail.home']);
    $routes->post('save','Settings\MailConfig::save', ['as' => 'mail.save']);
    $routes->get('oauth2/configure','Settings\MailConfig::config', ['as' => 'mail.config']);
    $routes->get('oauth2/reset-token','Settings\MailConfig::reset', ['as' => 'mail.reset']);
});

/* Permission */
$routes->group('settings/permissions', ['filter' => 'checkauth'], static function ($routes) {
    $routes->get('/', 'Settings\Permission::index', ['as' => 'permission.home']);
    $routes->post('list', 'Settings\Permission::list', ['as' => 'permission.list']);
    $routes->post('save', 'Settings\Permission::save', ['as' => 'permission.save']);
    $routes->post('edit', 'Settings\Permission::edit', ['as' => 'permission.edit']);
    $routes->post('delete', 'Settings\Permission::delete', ['as' => 'permission.delete']);
});

/* Access denied */
$routes->get('access-denied','Settings\Permission::denied', ['as' => 'access.denied']);

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
