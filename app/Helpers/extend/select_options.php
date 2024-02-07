<?php
if (! function_exists('inventory_categories_options'))
{
    /**
     * Get inventory dropdowns
     */
	function inventory_categories_options(object $model, bool $all = false): string
	{
		$option     = '';
        $others     = '';
        $columns    = 'dropdown_id, dropdown, other_category_type';
        $categories = $model->getDropdowns('CATEGORY', $columns, $all);

        if (! empty($categories)) {
            foreach ($categories as $category) {
                if (empty($category['other_category_type'])) {
                    $option     .= '
                        <option value="'. $category['dropdown_id'] .'">
                            '. $category['dropdown'] .'
                        </option>
                    ';
                } else {
                    $others     .= '
                        <option value="other__'. $category['dropdown_id'] .'">
                            '. $category['dropdown'] .'
                        </option>
                    ';
                }
            }

            $option .= $others ? '<optgroup label="Other Categories">'. $others .'</optgroup>' : '';
        }

        return $option;
    }
}

if (! function_exists('get_work_type'))
{
    /**
     * Get work type of Job Order module
     */
	function get_work_type(string $param = ''): string|array
	{
        $options = [
            'Phone Support' => 'Phone Support',
            'Service'       => 'Service',
            'Installation'  => 'Installation',
        ];

        return $param ? $options[$param] : $options;
	}
}

if (! function_exists('get_jo_status'))
{
    /**
     * Get status of Job Order module
     */
	function get_jo_status(string $param = '', bool $pass_tense = false): string|array
	{
        $options = [
            'pending'   => 'pending',
            'accept'    => 'accept',
            'file'      => 'file',
            'discard'   => 'discard',
        ];

        $options_pt = [
            'pending'   => 'pending',
            'accepted'  => 'accepted',
            'filed'     => 'filed',
            'discarded' => 'discarded',
        ];

        $arr = $pass_tense ? $options_pt : $options;

        return $param ? $arr[strtolower($param)] : $arr;
	}
}

if (! function_exists('set_jo_status'))
{
    /**
     * Setting status of Job Order module to its past tense
     */
	function set_jo_status(string|array $param): string|array
	{
        $options = [
            'pending'   => 'pending',
            'accept'    => 'accepted',
            'accepted'  => 'accepted',
            'file'      => 'filed',
            'filed'     => 'filed',
            'discard'   => 'discarded',
            'discarded' => 'discarded',
        ];

        if (is_array($param)) {
            $arr = [];
            foreach ($param as $val) {
                $arr[] = set_jo_status($val);
            }

            return $arr;
        }

        return $options[strtolower($param)] ?? strtolower($param);
	}
}

if (! function_exists('get_tasklead_type'))
{
    /**
     * Get tasklead type of Tasklead module
     */
	function get_tasklead_type(string $param = ''): string|array
	{
        $options = [
            'Project'   => 'Project',
            'Service'   => 'Service',
            'Supplies'  => 'Supplies',
        ];

        return $param ? $options[$param] : $options;
	}
}

if (! function_exists('get_tasklead_status'))
{
    /**
     * Get tasklead type of Tasklead module
     */
	function get_tasklead_status(string $param = '', bool $booked = false): string|array
	{
        $options = [
            '10.00%' => 'Identified (10%)',
            '30.00%' => 'Qualified (30%)',
            '50.00%' => 'Developed Solution (50%)',
            '70.00%' => 'Evaluation (70%)',
            '90.00%' => 'Negotiation (90%)',
            '100.00%' => 'Booked (100%)',
        ];

        if (! $booked) unset($options['100.00%']);

        return $param ? $options[$param] : $options;
	}
}

if (! function_exists('get_quotation_type'))
{
    /**
     * Get tasklead quotation type of Tasklead module
     */
	function get_quotation_type(string $param = ''): string|array
	{
        $options = [
            'Q1' => 'Supplies',
            'Q2' => 'Service',
            'Q3' => 'Project',
        ];

        return $param ? $options[strtoupper($param)] : $options;
	}
}

if (! function_exists('get_quotation_color'))
{
    /**
     * Get tasklead quotation type of Tasklead module
     */
	function get_quotation_color(string $param = ''): string|array
	{
        $options = [
            'B' => 'Blue',
            'G' => 'Green',
            'Y' => 'Yellow',
            'O' => 'Orange',
            'R' => 'Red',
        ];

        return $param ? $options[strtoupper($param)] : $options;
	}
}

if (! function_exists('get_schedule_type'))
{
    /**
     * Get schedule type of Schedule module
     */
	function get_schedule_type(string $param = '', $with_out_color = false): string|array
	{
        $options = [
            'installation'  => [
                'text'      => 'Installation',
                'color'     => '#0073b7', //Blue
            ],
            'service'       => [
                'text'      => 'Service',
                'color'     => '#f39c12', //yellow
            ],
            'supplies'      => [
                'text'      => 'Supplies',
                'color'     => '#3c8dbc', //Primary (light-blue)
            ],
            'payables'      => [
                'text'      => 'Payables',
                'color'     => '#f56954', //red
            ],
            'holiday'       => [
                'text'      => 'Holiday/Event',
                'color'     => '#00a65a', //Success (green)
            ],
            'meetings'      => [
                'text'      => 'Meetings',
                'color'     => '#adb5bd', //Gray
            ],
            'deployment'      => [
                'text'      => 'Deployment',
                'color'     => '#00c0ef' //Info (aqua)
            ],
            'project'       => [
                'text'      => 'Project',
                'color'     => '#6610f2', //Indigo
            ],
            'turnover'      => [
                'text'      => 'Project Turn-Over',
                'color'     => '#3d9970' //Olive
            ],
        ];

        if ($with_out_color) {
            $arr = [];
            foreach ($options as $key => $val) {
                $arr[$key] = $val['text'];
            }

            $options = $arr;
        }

        return $param ? $options[strtolower($param)] : $options;
	}
}

if (! function_exists('get_dispatch_services'))
{
    /**
     * Get dispatch services of Dispatch module
     */
	function get_dispatch_services(string $param = ''): string|array
	{
        $options = [
            'installation'  => 'Installation',
            'service'       => 'Service',
            'warranty'      => 'Warranty',
            'backjob'       => 'Back Job',
        ];

        return $param ? $options[strtolower($param)] : $options;
	}
}

if (! function_exists('get_prf_status'))
{
    /**
     * Get status of Project Request Form module
     */
	function get_prf_status(string $param = '', bool $pass_tense = false): string|array
	{
        $options = [
            'pending'   => 'pending',
            'accept'    => 'accept',
            'reject'    => 'reject',
            'receive'   => 'receive',
            'item_out'  => 'item_out',
            'file'      => 'file',
        ];

        $options_pt = [
            'pending'   => 'pending',
            'accepted'  => 'accepted',
            'rejected'  => 'rejected',
            'item_out'  => 'item_out',
            'received'  => 'received',
            'filed'     => 'filed',
        ];

        $arr = $pass_tense ? $options_pt : $options;

        return $param ? $arr[strtolower($param)] : $arr;
	}
}

if (! function_exists('set_prf_status'))
{
    /**
     * Setting status of Project Request Form module to its past tense
     */
	function set_prf_status(string|array $param = null): string|array
	{
        $options = [
            'pending'   => 'pending',
            'accept'    => 'accepted',
            'accepted'  => 'accepted',
            'reject'    => 'rejected',
            'rejected'  => 'rejected',
            'item_out'  => 'item_out',
            'receive'   => 'received',
            'received'  => 'received',
            'file'      => 'filed',
            'filed'     => 'filed',
        ];

        if (is_null($param)) {
            return $options;
        }

        if (is_array($param)) {
            $arr = [];
            foreach ($param as $val) {
                $arr[] = set_prf_status($val);
            }

            return $arr;
        }

        return $options[strtolower($param)] ?? strtolower($param);
	}
}

if (! function_exists('get_rpf_status'))
{
    /**
     * Get status of Request to Purchase Forms (RPF)
     */
	function get_rpf_status(string $param = '', bool $pass_tense = false): string|array
	{
        $options = [
            'pending'   => 'pending',
            'accept'    => 'accept',
            'reject'    => 'reject',
            'review'    => 'review',
        ];

        $options_pt = [
            'pending'   => 'pending',
            'accepted'  => 'accepted',
            'rejected'  => 'rejected',
            'reviewed'  => 'reviewed',
        ];

        $arr = $pass_tense ? $options_pt : $options;

        return $param ? $arr[strtolower($param)] : $arr;
	}
}

if (! function_exists('set_rpf_status'))
{
    /**
     * Setting status of Request to Purchase Forms (RPF) to its past tense
     */
	function set_rpf_status(string|array $param = null): string|array
	{
        $options = [
            'pending'   => 'pending',
            'accept'    => 'accepted',
            'accepted'  => 'accepted',
            'reject'    => 'rejected',
            'rejected'  => 'rejected',
            'review'    => 'reviewed',
            'reviewed'  => 'reviewed',
        ];

        if (is_null($param)) {
            return $options;
        }

        if (is_array($param)) {
            $arr = [];
            foreach ($param as $val) {
                $arr[] = set_rpf_status($val);
            }

            return $arr;
        }

        return $options[strtolower($param)] ?? strtolower($param);
	}
}

if (! function_exists('get_client_types'))
{
    /**
     * Get the types for client module
     */
	function get_client_types(string $param = ''): string|array
	{
        $options = [
            'COMMERCIAL'    => 'Commercial',
            'RESIDENTIAL'   => 'Residential'
        ];

        return $param ? $options[strtoupper($param)] : $options;
	}
}

if (! function_exists('get_client_sources'))
{
    /**
     * Get the sources for client module
     */
	function get_client_sources(string $param = ''): string|array
	{
        $options = [
            'BNI REFERRAL'          => 'BNI REFERRAL',
            'SOCIAL MEDIA'          => 'SOCIAL MEDIA',
            'WALK IN'               => 'WALK IN',
            'SATURATION'            => 'SATURATION',
            'THIRD PARTY REFERRAL'  => 'THIRD PARTY REFERRAL',
        ];

        return $param ? $options[strtoupper($param)] : $options;
	}
}

if (! function_exists('get_po_status'))
{
    /**
     * Get status of Purchase Order (PO)
     */
	function get_po_status(string $param = '', bool $pass_tense = false): string|array
	{
        $options = [
            'pending'   => 'pending',
            'approve'   => 'approve',
            'receive'   => 'receive',
        ];

        $options_pt = [
            'pending'   => 'pending',
            'approved'  => 'approved',
            'received'  => 'received',
        ];

        $arr = $pass_tense ? $options_pt : $options;

        return $param ? $arr[strtolower($param)] : $arr;
	}
}

if (! function_exists('set_po_status'))
{
    /**
     * Setting status of Purchase Order (PO) to its past tense
     */
	function set_po_status(string|array $param = null): string|array
	{
        $options = [
            'pending'   => 'pending',
            'approve'   => 'approved',
            'approved'  => 'approved',
            'receive'   => 'received',
            'received'  => 'received',
        ];

        if (is_null($param)) {
            return $options;
        }

        if (is_array($param)) {
            $arr = [];
            foreach ($param as $val) {
                $arr[] = set_po_status($val);
            }

            return $arr;
        }

        return $options[strtolower($param)] ?? strtolower($param);
	}
}

if (! function_exists('get_supplier_type'))
{
    /**
     * Get supplier type of Supplier module
     */
	function get_supplier_type(string $param = ''): string|array
	{
        $options = [
            'Direct'            => 'Direct',
            'Indirect'          => 'Indirect',
            'Tools Supplier'    => 'Tools Supplier',
            'Office Assets'     => 'Office Assets',
            'Others'            => 'Others',
        ];

        return $param ? $options[$param] : $options;
	}
}

if (! function_exists('get_payment_terms'))
{
    /**
     * Payment terms.
     * Used in module(s): Supplier
     */
	function get_payment_terms(string $param = '', bool $is_filter = false): string|array
	{
        $word   = ' DAYS';
        $arr    = $is_filter ? ['zero' => 'N/A'] : ['0' => 'N/A'];
        $terms  = $arr + [
            '7'   => '7'. $word,
            '15'  => '15'. $word,
            '21'  => '21'. $word,
            '30'  => '30'. $word,
            '45'  => '45'. $word,
            '50'  => '50'. $word,
            '60'  => '60'. $word,
            '90'  => '90'. $word,
            '120'  => '120'. $word,
        ];

        if (is_array($param)) {
            $arr = [];
            foreach ($param as $val) {
                $arr[] = set_prf_status($val);
            }

            return $arr;
        }

        return $param ? $terms[$param] : $terms;
	}
}

if (! function_exists('get_supplier_mop'))
{
    /**
     * Get supplier mode of payment of Supplier module
     */
	function get_supplier_mop(string $param = ''): string|array
	{
        $options = [
            'Cash'              => 'Cash',
            'Check'             => 'Check',
            'Online Payment'    => 'Online Payment',
            'Dated Check'       => 'Dated Check',
            'Others'            => 'Others',
        ];

        return $param ? $options[$param] : $options;
	}
}

if (! function_exists('get_employment_status'))
{
    /**
     * Get employment status of Employee module
     */
	function get_employment_status(string $param = ''): string|array
	{
        $options = [
            'Probation'     => 'Probation',
            'Regular'       => 'Regular',
            'Contractual'   => 'Contractual',
            'Temporary'     => 'Temporary',
            'Project-based' => 'Project-Based',
            'Resigned'      => 'Resigned',
        ];

        return $param ? $options[$param] : $options;
	}
}

if (! function_exists('get_quarters'))
{
    /**
     * Get quarters of Tasklead module
     */
	function get_quarters(string $param = ''): string|array
	{
        $options = [
            1 => '1st Quarter',
            2 => '2nd Quarter',
            3 => '3rd Quarter',
            4 => '4th Quarter',
        ];

        return $param ? $options[$param] : $options;
	}
}

if (! function_exists('get_salary_rate_type'))
{
    /**
     * Get salary rate type of Payroll/Salary Rate module
     */
	function get_salary_rate_type(string $param = ''): string|array
	{
        $options = [
            'Hourly'    => 'Hourly Rate',
            'Daily'     => 'Daily Rate',
            'Monthly'   => 'Monthly Rate',
        ];

        return $param ? $options[$param] : $options;
	}
}

if (! function_exists('get_leave_type'))
{
    /**
     * Get leave type of Payroll/Manage Leave module
     */
	function get_leave_type(string $param = ''): string|array
	{
        $options = [
            'Leave of Absence'  => 'Leave of Absence',
            'SIL'               => 'Service Incentive Leave (SIL)',
        ];

        return $param ? $options[$param] : $options;
	}
}

if (! function_exists('set_leave_status'))
{
    /**
     * Setting status of Leave to its past tense
     */
	function set_leave_status(string|array $param): string|array
	{
        $options = [
            'pending'   => 'pending',
            'discard'   => 'discarded',
            'process'   => 'processed',
            'approve'   => 'approved',
        ];

        if (is_array($param)) {
            $arr = [];
            foreach ($param as $val) {
                $arr[] = set_leave_status($val);
            }

            return $arr;
        }

        return $options[strtolower($param)] ?? strtolower($param);
	}
}

if (! function_exists('get_leave_status'))
{
    /**
     * Getting leave statuses
     */
	function get_leave_status(string $param = '', bool $pass_tense = false): string|array
	{
        $options = [
            'pending'   => 'pending',
            'discard'   => 'discard',
            'process'   => 'process',
            'approve'   => 'approve',
        ];

        $options_pt = [
            'pending'   => 'pending',
            'discarded' => 'discarded',
            'processed' => 'processed',
            'approved'  => 'approved',
        ];

        $arr = $pass_tense ? $options_pt : $options;

        return $param ? $arr[strtolower($param)] : $arr;
	}
}

if (! function_exists('get_days'))
{
    /**
     * Get days for Payroll/Settings module
     */
	function get_days(string $param = ''): string|array
	{
        $options = [
            'Monday'    => 'Monday',
            'Tuesday'   => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday'  => 'Thursday',
            'Friday'    => 'Friday',
            'Saturday'  => 'Saturday',
            'Sunday'    => 'Sunday',
        ];

        return $param ? $options[$param] : $options;
	}
}

if (! function_exists('get_bill_types'))
{
    /**
     * Get bill types for Finance/Billing Invoice module
     */
	function get_bill_types(string $param = ''): string|array
	{
        $options = [
            'Down Payment'      => 'Down Payment',
            'Progress Billing'  => 'Progress Billing',
            'Final Payment'     => 'Final Payment',
        ];

        return $options[$param] ?? $options;
	}
}

if (! function_exists('get_billing_status'))
{
    /**
     * Get billing status for Finance/Billing Invoice module
     */
	function get_billing_status(string $param = ''): string|array
	{
        $options = [
            'pending'   => 'Pending',
            'overdue'   => 'Overdue',
            'paid'      => 'Paid',
        ];

        return $options[$param] ?? $options;
	}
}

if (! function_exists('get_expenses'))
{
    /**
     * Get expenses type for Finance/Funds module
     */
	function get_expenses(string $param = ''): string|array
	{
        $options = [
            'Petty Cash'        => 'Petty Cash',
            'Salary'            => 'Salary',
            'Purchase Orders'   => 'Purchase Orders',
            'Loans Payment'     => 'Loans Payment',
            'Advances'          => 'Advances',
        ];

        return $options[$param] ?? $options;
	}
}