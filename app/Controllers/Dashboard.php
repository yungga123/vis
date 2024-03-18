<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Models\BillingInvoiceModel;
use App\Models\EmployeeModel;
use App\Models\CustomerModel;
use App\Models\DispatchModel;
use App\Models\JobOrderModel;
use App\Models\ScheduleModel;
use App\Models\InventoryModel;
use App\Models\LeaveModel;
use App\Models\OvertimeModel;
use App\Models\PayrollModel;
use App\Models\ProjectRequestFormModel;
use App\Models\TaskLeadModel;
use App\Models\TaskLeadView;
use App\Models\SuppliersModel;
use App\Models\RequestPurchaseFormModel;
use App\Models\PurchaseOrderModel;
use App\Models\RolesModel;
use App\Models\PermissionModel;
use App\Models\SalaryRateModel;
use App\Models\TimesheetModel;
use App\Traits\AdminTrait;
use App\Traits\PayrollSettingTrait;

class Dashboard extends BaseController
{
    /* Declare trait here to use */
    use AdminTrait, PayrollSettingTrait;
    
    /**
     * Use to get the permissions
     * @var array
     */
    private $_permissions;

    /**
     * Display the index view
     *
     * @return view
     */
    public function index()
    {
        $data['title']          = 'Dashboard';
        $data['page_title']     = 'Dashboard';
        $data['sweetalert2']    = true;
        $data['toastr']         = true;
        $data['modules']        = $this->_moduleBoxMenu();
        $data['type_legend']    = $this->scheduleTypeLegend();
        $data['schedules']      = $this->getSchedulesForToday(true);
        $data['office_hours']   = $this->getOfficeHours(true);
        $data['custom_js']      = [
            'dashboard/index.js',
            'payroll/timesheet/common.js',
        ];
        $data['routes']         = json_encode([
            'payroll' => [
                'timesheet' => [
                    'fetch' => url_to('payroll.timesheet.fetch'),
                    'clock' => url_to('payroll.timesheet.clock'),
                ],
            ],
        ]);

        return view('dashboard/index', $data);
    }

    /**
     * Get the module card menu
     *
     * @return string (html)
     */
    public function _moduleBoxMenu()
    {
        $arr            = [];
        $modules        = $this->modules;
        $permissions    = format_results($this->permissions, 'module_code', 'permissions');

        if (! empty($modules) && is_array($modules)) {
            // Sort modules ascending
            sort($modules);

            $setup_modules  = array_keys(setup_modules());
            $record_counts  = $this->_recordCounts();
            $headers        = [];

            foreach ($modules as $val) {
                $_perms = isset($permissions[$val]) ? $permissions[$val] : get_generic_modules_actions($val);
                $_perms = is_array($_perms) ? $_perms : explode(',', $_perms);

                // Check if actions VIEW OR VIEW_ALL are in the $_perms
                $value  = array_intersect([ACTION_VIEW, ACTION_VIEW_ALL], $_perms);

                // If user is not an admin and has no VIEW permission
                // for this module, then continue to the next loop
                if (! is_admin() && empty($value)) {
                    continue;
                }

                $this->_permissions[$val] = is_admin() ? ['VIEW'] : $_perms;

                // Not include DASHBOARD module                
                if ($val !== 'DASHBOARD' && in_array($val, $setup_modules)) {
                    $module         = setup_modules($val);
                    $module_name    = $module['name'];
                    $menu           = empty($module['menu']) ? $val : $module['menu'];
                    $count          = '';
                    $more_info      = '';
                    $header         = $module['header'] ?? '';
                    $_header        = '';

                    if (! empty($header) && ! in_array($header, $headers)) {
                        $headers[$header]   = $header;
                        $_header            = '<h5 class="mt-3">'.strtoupper($header).'</h5>';
                    }

                    if (isset($record_counts[$val])) {
                        $param = $record_counts[$val];
                        $count = $param;

                        if (is_array($param)) {
                            $count          = $param['count'];
                            $module_name    = isset($param['name']) ? $param['name'] : $module_name;

                            if (isset($param['more_info'])) {
                                foreach ($param['more_info'] as $key => $value) {
                                    $bg         = isset($value['bg']) ? $value['bg'] : 'info';
                                    $icon       = isset($value['icon']) ? $value['icon'] : $module['icon'];
                                    $text       = ucwords(str_replace('_', ' ', $key));
                                    $text       = isset($value['link']) ? "<a href='{$value['link']}'>{$text}</a>" : $text;
                                    $more_info  .= <<<EOF
                                        <div class="info-box text-dark">
                                            <span class="info-box-icon bg-{$bg}">
                                                <i class="{$icon}"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">{$text}</span>
                                                <span class="info-box-number mt-0">{$value['count']}</span>
                                            </div>
                                        </div>
                                    EOF;
                                }
                            }
                        }

                        $count = "<h3 class='mb-0'>{$count}</h3>";
                    }

                    $action = "href='{$module['url']}'";
                    if (! empty($more_info)) {
                        $more_info = <<<EOF
                            <div class="d-none bg-white px-3 pt-3 pb-1" id="{$val}_MORE_INFO">
                                {$more_info}
                            </div>
                            <a href="{$module['url']}" class="small-box-footer bg-info d-none" id="{$val}_LINK">
                                Go to module
                            </a>
                        EOF;
                        $action = <<<EOF
                            onclick="toggleMoreInfo('{$val}')" type="button"
                        EOF;
                    }

                    // Add module card menu
                    $card = <<<EOF
                        {$_header}
                        <div class="small-box bg-success">
                            <div class="inner">
                                {$count}
                                <h5>{$module_name}</h5>
                            </div>
                            <div class="icon"><i class="{$module['icon']}"></i></div>
                            <a $action class="small-box-footer">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </a>
                            {$more_info}
                        </div>
                    EOF;

                    // Store in array based on menu
                    $arr[$menu][] = $card;
                }
            }
        }

        return $this->_cardHtml($arr);
    }

    /**
     * Get the whole card box html
     *
     * @return string (html)
     */
    private function _cardHtml($arr)
    {
        $html = '';    

        if (!empty($arr)) {
            $modules = get_modules();

            foreach ($arr as $key => $val) {
                $box    = implode('', $val);
                $title  = isset($modules[$key]) ? get_modules($key) : get_nav_menus($key)['name'];
                $title  = $key === 'INVENTORY' ? 'Inventory' : $title;
                $html   .= <<<EOF
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">{$title}</h5>
                            </div>
                            <div class="card-body">
                                {$box}
                            </div>
                        </div>
                    </div>	
                EOF;
           }
        } else $html = '<h2>No module card to be displayed!</h2>';

        return $html;
    }

    /**
     * Get the whole card box html
     *
     * @return array
     */
    private function _recordCounts()
    {
        // Models initialization
        $accountModel       = new AccountModel();
        $employeeModel      = new EmployeeModel();
        $customerModel      = new CustomerModel();
        $dispatchModel      = new DispatchModel();
        $jobOrderModel      = new JobOrderModel();
        $scheduleModel      = new ScheduleModel();
        $inventoryModel     = new InventoryModel();
        $prfModel           = new ProjectRequestFormModel();
        $supplierModel      = new SuppliersModel();
        $rpfModel           = new RequestPurchaseFormModel();
        $poModel            = new PurchaseOrderModel();
        $taskLeadModel      = new TaskLeadModel();
        $taskLeadView       = new TaskLeadView();
        $rolesModel         = new RolesModel();
        $permissionModel    = new PermissionModel();
        $leaveModel         = new LeaveModel();
        $overtimeModel      = new OvertimeModel();
        $payrollModel       = new PayrollModel();
        $timesheetModel     = new TimesheetModel();
        $salaryRateModel    = new SalaryRateModel();
        $billingModel       = new BillingInvoiceModel();

        // Count all queries
        $accountCount           = $accountModel->where('deleted_at IS NULL');
        $employeeCount          = $employeeModel->where('deleted_at IS NULL');
        $dispatchCount          = $dispatchModel->where('deleted_at IS NULL')->countAllResults();
        $scheduleCount          = $scheduleModel->where('deleted_at IS NULL')->countAllResults();
        $inventoryCount         = $inventoryModel->where('deleted_at IS NULL')->countAllResults();
        $supplierCount          = $supplierModel->where('deleted_at IS NULL')->countAllResults();
        $taskLeadBookedCount    = $taskLeadView->where('deleted_at IS NULL')->countAllResults();
        $rolesCount             = $rolesModel->where('deleted_at IS NULL')->countAllResults();
        $permissionCount        = $permissionModel->where('deleted_at IS NULL')->countAllResults();

        if (! is_admin()) {
            $accountCount->whereNotIn('UPPER(access_level)', [strtoupper(AAL_ADMIN)]);
            $employeeCount->where('employee_id !=', DEVELOPER_ACCOUNT);
        }

        // Format record counts by module code
        $recordCounts = [
            'ACCOUNTS'              => $accountCount->countAllResults(),
            'EMPLOYEES'             => $employeeCount->countAllResults(),
            'CUSTOMERS'             => [
                'count'     => $customerModel->countRecords(),
                'more_info' => [
                    'commercial' => [
                        'icon'  => 'far fa-address-card',
                        'count' => $customerModel->countRecords('COMMERCIAL'),
                        'bg'    => 'success',
                    ],
                    'residential' => [
                        'icon'  => 'far fa-address-book',
                        'count' => $customerModel->countRecords('RESIDENTIAL'),
                        'bg'    => 'primary',
                    ],
                ]
            ],
            'ADMIN_DISPATCH'        => $dispatchCount,
            'ADMIN_JOB_ORDER'       => [
                'name'  => 'Pending Job Order',
                'count' => $jobOrderModel->countRecords('pending'),
            ],
            'ADMIN_SCHEDULES'       => [
                'count'     => $scheduleCount,
                'more_info' => [
                    'today' => [
                        'count' => $scheduleModel->getSchedulesForToday(true),
                    ],
                ]
            ],
            'INVENTORY'             => [
                'count'     => $inventoryCount,
                'more_info' => [
                    'total QTY of all items' => [
                        'count' => $inventoryModel->getItemTotalStocks(),
                    ],
                ],
            ],
            'INVENTORY_PRF'         => [
                'count'     => $prfModel->countRecords(),
                'more_info' => [
                    'pending' => [
                        'icon'  => 'far fa-clock',
                        'count' => $prfModel->countRecords('pending'),
                        'bg'    => 'warning',
                    ],
                    'accepted' => [
                        'icon'  => 'fas fa-check-circle',
                        'count' => $prfModel->countRecords('accepted'),
                        'bg'    => 'primary',
                    ],
                    'item_out' => [
                        'icon'  => 'fas fa-file-import',
                        'count' => $prfModel->countRecords('item_out'),
                        'bg'    => 'success',
                    ],
                    'filed' => [
                        'icon'  => 'fas fa-file-alt',
                        'count' => $prfModel->countRecords('filed'),
                        'bg'    => 'dark',
                    ],
                    'rejected' => [
                        'icon'  => 'fas fa-times-circle',
                        'count' => $prfModel->countRecords('rejected'),
                        'bg'    => 'secondary',
                    ],
                ]
            ],
            'PURCHASING_SUPPLIERS'  => $supplierCount,
            'PURCHASING_RPF'        => [
                'count'     => $rpfModel->countRecords(),
                'more_info' => [
                    'pending' => [
                        'icon'  => 'far fa-clock',
                        'count' => $rpfModel->countRecords('pending'),
                        'bg'    => 'warning',
                    ],
                    'accepted' => [
                        'icon'  => 'fas fa-check-circle',
                        'count' => $rpfModel->countRecords('accepted'),
                        'bg'    => 'primary',
                    ],
                    'reviewed' => [
                        'icon'  => 'fas fa-check-double',
                        'count' => $rpfModel->countRecords('reviewed'),
                        'bg'    => 'success',
                    ],
                    'rejected' => [
                        'icon'  => 'fas fa-times-circle',
                        'count' => $rpfModel->countRecords('rejected'),
                        'bg'    => 'secondary',
                    ],
                ]
            ],
            'PURCHASING_PO'         => [
                'count'     => $poModel->countRecords(),
                'more_info' => [
                    'pending'   => [
                        'icon'  => 'far fa-clock',
                        'count' => $poModel->countRecords('pending'),
                        'bg'    => 'warning',
                    ],
                    'approved'  => [
                        'icon'  => 'fas fa-check-circle',
                        'count' => $poModel->countRecords('approved'),
                        'bg'    => 'primary',
                    ],
                    'received'  => [
                        'icon'  => 'fas fa-file-import',
                        'count' => $poModel->countRecords('received'),
                        'bg'    => 'success',
                    ],
                ]
            ],
            'TASK_LEAD'             => [
                'count'     => $taskLeadModel->countRecords(),
                'more_info' => [
                    'identified (10%)' => [
                        'count' => $taskLeadModel->countRecords('10'),
                    ],
                    'qualified (30%)' => [
                        'count' => $taskLeadModel->countRecords('30'),
                    ],
                    'develop solution (50%)' => [
                        'count' => $taskLeadModel->countRecords('50'),
                    ],
                    'evaluation (70%)' => [
                        'count' => $taskLeadModel->countRecords('70'),
                    ],
                    'negotiation (90%)' => [
                        'count' => $taskLeadModel->countRecords('90'),
                    ],
                    'booked (100%)' => [
                        'count' => $taskLeadBookedCount,
                        'bg'    => 'success',
                        'link'  => url_to('tasklead.booked.home'),
                    ],
                ]
            ],
            'SETTINGS_ROLES'        => $rolesCount,
            'SETTINGS_PERMISSIONS'  => $permissionCount,
            'PAYROLL_LEAVE'         => $leaveModel->countRecords(true),
            'PAYROLL_OVERTIME'      => $overtimeModel->countRecords(true),
            'PAYROLL_PAYSLIP'       => $payrollModel->countRecords(true),
            'PAYROLL_TIMESHEETS'    => $timesheetModel->countRecords(true),
            'PAYROLL_SALARY_RATES'  => $salaryRateModel->countRecords(),
            'FINANCE_BILLING_INVOICE' => [
                'count'     => $billingModel->countRecords(),
                'more_info' => [
                    'pending'   => [
                        'count' => $billingModel->countRecords('pending'),
                        'bg'    => 'warning',
                    ],
                    'overdue'   => [
                        'count' => $billingModel->countRecords('overdue'),
                        'bg'    => 'danger',
                    ],
                    'paid'      => [
                        'count' => $billingModel->countRecords('paid'),
                        'bg'    => 'success',
                    ],
                ]
            ],
        ];

        if (is_admin() || in_array(ACTION_VIEW_ALL, ($this->_permissions['PAYROLL_LEAVE'] ?? []))) {
            $recordCounts['PAYROLL_LEAVE'] = [
                'count'     => $leaveModel->countRecords(),
                'more_info' => [
                    'my leave'  => [
                        'count' => $leaveModel->countRecords(true),
                    ],
                ]
            ];
        }

        if (is_admin() || in_array(ACTION_VIEW_ALL, ($this->_permissions['PAYROLL_OVERTIME'] ?? []))) {
            $recordCounts['PAYROLL_OVERTIME'] = [
                'count'     => $overtimeModel->countRecords(),
                'more_info' => [
                    'my overtime'  => [
                        'count' => $overtimeModel->countRecords(true),
                    ],
                ]
            ];
        }

        if (is_admin() || in_array(ACTION_VIEW_ALL, ($this->_permissions['PAYROLL_PAYSLIP'] ?? []))) {
            $recordCounts['PAYROLL_PAYSLIP'] = [
                'count'     => $payrollModel->countRecords(),
                'more_info' => [
                    'my payslip'  => [
                        'count' => $payrollModel->countRecords(true),
                    ],
                ]
            ];
        }

        if (is_admin() || in_array(ACTION_VIEW_ALL, ($this->_permissions['PAYROLL_TIMESHEETS'] ?? []))) {
            $recordCounts['PAYROLL_TIMESHEETS'] = [
                'count'     => $timesheetModel->countRecords(),
                'more_info' => [
                    'my timesheets'  => [
                        'count' => $timesheetModel->countRecords(true),
                    ],
                ]
            ];
        }

        return $recordCounts;
    }
}