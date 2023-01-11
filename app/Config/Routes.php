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
$routes->get('/', 'Dashboard::index');
$routes->get('/login', 'LoginPage::index');
$routes->get('/login_validate', 'LoginPage::sign_in');
$routes->post('/login_validate', 'LoginPage::sign_in');

//TEST
$routes->get('/test',"Test::index");

//LOG OUT
$routes->get('/logout',"LoginPage::logout");

//DASHBOARD ROUTE
$routes->get('/dashboard','Dashboard::index');

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

//ACCOUNTS
$routes->get('/add-account','Accounts::index');
$routes->post('/post-add-account','Accounts::add_account_validate');
$routes->get('/list-account','Accounts::list_account');
$routes->get('/ajax-account','Accounts::get_accounts');
$routes->get('edit-account/(:num)','Accounts::edit_account/$1');
$routes->post('/post-edit-account','Accounts::edit_account_validate');
$routes->get('delete-account/(:num)','Accounts::delete_account/$1');

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
