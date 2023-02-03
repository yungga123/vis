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
$routes->get('/login_validate', 'LoginPage::sign_in', ['filter' => 'notlogged']);
$routes->post('/login_validate', 'LoginPage::sign_in', ['filter' => 'notlogged']);

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
$routes->get('/sales-dashboard','SalesDashboard::index');

//ADMIN DASHBOARD
$routes->get('/admin-dashboard','AdminDashboard::index');

//EXECUTIVE OVERVIEW
$routes->get('/executive-overview','ExecutiveOverview::index');

//CUSTOMERS
$routes->get('/add-customer','Customers::index');
$routes->get('/list-customer','Customers::customer_table');
$routes->post('/add-customers','Customers::add_customers');
$routes->get('/customer-list','Customers::getCustomers');
$routes->get('/edit-customer/(:num)','Customers::edit_customers/$1');
$routes->post('/edit-customer-val','Customers::edit_customers_validate');
$routes->get('/delete-customer/(:num)','Customers::delete_customer/$1');
$routes->get('/customers-list','Customers::customers_list');

//CUSTOMERS BRANCH
$routes->get('/add-customer-branch','CustomerBranch::index');
$routes->post('/add-customerbranch','CustomerBranch::add_customer_validate');
$routes->get('/edit-customerbranch/(:num)','CustomerBranch::edit_customer_branch/$1');
$routes->post('/edit-customerbranch/(:num)','CustomerBranch::edit_customer_branch/$1');
$routes->get('/delete-customer-branch/(:num)','CustomerBranch::delete_customer_branch/$1');

//TaskLead
$routes->get('/tasklead','TaskLead::index');
$routes->get('/tasklead-addproject','TaskLead::add_project');
$routes->get('/tasklead-editproject/(:num)','TaskLead::edit_project/$1');
$routes->post('/post-addproject','TaskLead::add_project_validate');
$routes->post('/post-editproject','TaskLead::edit_project_validate');
$routes->get('/project-list','TaskLead::project_list');
$routes->get('/manager-project-list','TaskLead::manager_project_list');
$routes->get('/project-list-booked','TaskLead::project_list_booked');
$routes->get('/manager-project-list-booked','TaskLead::manager_project_list_booked');
$routes->get('/project-table-booked','TaskLead::getProjectBookedList');
$routes->get('/manager-project-table-booked','TaskLead::getProjectListBookedManager');
$routes->get('/project-table','TaskLead::getProjectList');
$routes->get('/manager-project-table','TaskLead::getProjectListManager');
$routes->get('/delete-tasklead/(:num)','Tasklead::delete_tasklead/$1');
$routes->get('/update-tasklead/(:num)/(:any)','Tasklead::update_project_status/$1/$2');
$routes->get('/booked-status/(:num)','TaskLead::booked_status/$1');
$routes->post('/post-booked-status','TaskLead::booked_status_validate');
$routes->get('/project-booked-details/(:num)','TaskLead::project_booked_details/$1');
$routes->post('/post-tasklead-upload/(:num)','Tasklead::upload/$1');
$routes->get('/add-project','TaskLead::add_identified');
$routes->post('/add-project','TaskLead::add_identified');
$routes->post('post-update-project-status','TaskLead::update_project_status_validate');
$routes->get('/add-project-existingcustomer','TaskLead::add_projectExistingCustomer');
$routes->post('/add-project-existingcustomer','TaskLead::add_projectExistingCustomer');



//EMPLOYEES
$routes->get('/add-employee','Employees::index');
$routes->get('/employee-menu','Employees::employee_menu');
$routes->post('/employee-add','Employees::employee_add');
$routes->get('/employees','Employees::getEmployees');
$routes->get('/employee-list','Employees::employees_list');
$routes->get('/edit-employee/(:num)','Employees::edit_employee/$1');
$routes->post('/employee-edit','Employees::employee_edit');
$routes->get('/delete-employee/(:num)','Employees::delete_employee/$1');

//SALES MANAGER
$routes->get('/manager-of-sales','SalesManager::index');
$routes->get('/consolidated-sales-forecast','SalesManager::consolidated_forecast');

//ACCOUNTS
$routes->get('/add-account','Accounts::index', ['filter' => 'checkauth']);
$routes->post('/post-add-account','Accounts::add_account_validate', ['filter' => 'checkauth']);
$routes->get('/list-account','Accounts::list_account', ['filter' => 'checkauth']);
$routes->post('/ajax-account','Accounts::get_accounts', ['filter' => 'checkauth']);
$routes->get('edit-account/(:num)','Accounts::edit_account/$1', ['filter' => 'checkauth']);
$routes->post('/post-edit-account','Accounts::edit_account_validate', ['filter' => 'checkauth']);
$routes->get('delete-account/(:num)','Accounts::delete_account/$1', ['filter' => 'checkauth']);

// ACCOUNT PROFILE
# Filter 'checkauth' will check whether account is logged in or not for this route group 'account'
# and no need to individual add the filter in every routes
$routes->group('account', ['filter' => 'checkauth'], static function ($routes) {
    $routes->get('profile','AccountProfile::index', ['as' => 'account.profile']);
    $routes->post('change-password','AccountProfile::change_password', ['as' => 'account.change_pass']);
});


//CUSTOMERS VT
$routes->get('/customersvt_menu','CustomersVt::index');
$routes->get('/add-customervt','CustomersVt::add_customervt');
$routes->post('/add-customervt','CustomersVt::add_customervt');
$routes->get('/customervt-list','CustomersVt::customervt_list');
$routes->get('/add_customervtbranch','CustomersVt::add_customervtbranch');
$routes->post('/add_customervtbranch','CustomersVt::add_customervtbranch');
$routes->get('/edit-customervtbranch/(:num)','CustomersVt::edit_customervtbranch/$1');
$routes->post('/edit-customervtbranch/(:num)','CustomersVt::edit_customervtbranch/$1');
$routes->get('/edit-customervt/(:num)','CustomersVt::edit_customervt/$1');
$routes->post('/edit-customervt/(:num)','CustomersVt::edit_customervt/$1');
$routes->get('/delete-customervt/(:num)','CustomersVt::delete_customervt/$1');
$routes->get('/delete-customervtbranch/(:num)','CustomersVt::delete_customervt_branch/$1');


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
$routes->get('settings/mail','Settings\MailConfig::index', [
    'filter'    => 'checkauth',
    'as'        => 'mail.home',
]);
$routes->group('settings/mail', ['filter' => 'checkauth'], static function ($routes) {
    $routes->post('save','Settings\MailConfig::save', ['as' => 'mail.save']);
    $routes->get('oauth2/configure','Settings\MailConfig::config', ['as' => 'mail.config']);
    $routes->get('oauth2/reset-token','Settings\MailConfig::reset', ['as' => 'mail.reset']);
});


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
