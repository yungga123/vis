<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\AdminTrait;
use App\Models\AccountModel;
use App\Models\EmployeeModel;
use App\Models\CustomerModel;
use App\Models\DispatchModel;
use App\Models\JobOrderModel;
use App\Models\ScheduleModel;
use App\Models\InventoryModel;
use App\Models\ProjectRequestFormModel;
use App\Models\TaskLeadModel;
use App\Models\TaskLeadView;
use App\Models\SuppliersModel;
use App\Models\RequestPurchaseFormModel;
use App\Models\PurchaseOrderModel;
use App\Models\RolesModel;
use App\Models\PermissionModel;

class Dashboard extends BaseController
{
    /* Declare trait here to use */
    use AdminTrait;

    /**
     * Display the index view
     *
     * @return view
     */
    public function index()
    {
        $data['title']          = 'Dashboard';
        $data['page_title']     = 'Dashboard';
        $data['exclude_toastr'] = true;
        $data['modules']        = $this->_moduleBoxMenu();
        $data['type_legend']    = $this->scheduleTypeLegend();
        $data['schedules']      = $this->getSchedulesForToday(true);
        $data['custom_js']      = 'dashboard/index.js';

        return view('dashboard/index', $data);
    }

    /**
     * Get the module card menu
     *
     * @return string (html)
     */
    public function _moduleBoxMenu()
    {
        $modules    = $this->modules;
        $arr        = [];

        if (! empty($modules) && is_array($modules)) {
            // Sort modules ascending
            sort($modules);

            $setup_modules = array_keys(setup_modules());
            $record_counts = $this->_recordCounts();

            foreach ($modules as $val) {
                // Not include DASHBOARD module                
                if ($val !== 'DASHBOARD' && in_array($val, $setup_modules)) {
                    $module = setup_modules($val);
                    $menu   = empty($module['menu']) ? $val : $module['menu'];
                    $count      = '';
                    $more_info  = '';

                    if (isset($record_counts[$val])) {
                        $param = $record_counts[$val];
                        $count = $param;
                        if (is_array($param)) {
                            $count = $param['count'];
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
                        <div class="small-box bg-success">
                            <div class="inner">
                                {$count}
                                <h5>{$module['name']}</h5>
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
                'count'     => $jobOrderModel->countRecords(),
                'more_info' => [
                    'pending' => [
                        'icon'  => 'far fa-clock',
                        'count' => $jobOrderModel->countRecords('pending'),
                        'bg'    => 'warning',
                    ],
                ]
            ],
            'ADMIN_SCHEDULES'       => [
                'count'     => $scheduleCount,
                'more_info' => [
                    'today' => [
                        'count' => $scheduleModel->getSchedulesForToday(true),
                    ],
                ]
            ],
            'INVENTORY'             => $inventoryCount,
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
                        'bg'    => 'info',
                    ],
                    'received' => [
                        'icon'  => 'fas fa-file-import',
                        'count' => $rpfModel->countRecords('received'),
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
                    'filed'     => [
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
        ];

        return $recordCounts;
    }
}