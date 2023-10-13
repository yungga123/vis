<?php
if (! function_exists('inventory_categories_options'))
{
    /**
     * Get inventory dropdowns
     */
	function inventory_categories_options($model, $all = false) 
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

        return $param ? $arr[$param] : $arr;
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

        return $options[$param];
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

if (! function_exists('get_quotation_type'))
{
    /**
     * Get tasklead quotation type of Tasklead module
     */
	function get_quotation_type(string $param = ''): string|array
	{
        $options = [
            'Q1' => 'Project',
            'Q2' => 'Service',
            'Q3' => 'Supplies',
        ];

        return $param ? $options[$param] : $options;
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

        return $param ? $options[$param] : $options;
	}
}

if (! function_exists('get_schedule_type'))
{
    /**
     * Get schedule type of Schedule module
     */
	function get_schedule_type(string $param = ''): string|array
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
            'item_out'  => 'item_out',
            'file'      => 'file',
        ];

        $options_pt = [
            'pending'   => 'pending',
            'accepted'  => 'accepted',
            'rejected'  => 'rejected',
            'item_out'  => 'item_out',
            'filed'     => 'filed',
        ];

        $arr = $pass_tense ? $options_pt : $options;

        return $param ? $arr[$param] : $arr;
	}
}

if (! function_exists('set_prf_status'))
{
    /**
     * Setting status of Project Request Form module to its past tense
     */
	function set_prf_status(string|array $param): string|array
	{
        $options = [
            'pending'   => 'pending',
            'accept'    => 'accepted',
            'accepted'  => 'accepted',
            'reject'    => 'rejected',
            'rejected'  => 'rejected',
            'item_out'  => 'item_out',
            'file'      => 'filed',
            'filed'     => 'filed',
        ];

        if (is_array($param)) {
            $arr = [];
            foreach ($param as $val) {
                $arr[] = set_prf_status($val);
            }

            return $arr;
        }

        return $options[$param];
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
            'receive'   => 'receive',
        ];

        $options_pt = [
            'pending'   => 'pending',
            'accepted'  => 'accepted',
            'rejected'  => 'rejected',
            'reviewed'  => 'reviewed',
            'received'  => 'received',
        ];

        $arr = $pass_tense ? $options_pt : $options;

        return $param ? $arr[$param] : $arr;
	}
}

if (! function_exists('set_rpf_status'))
{
    /**
     * Setting status of Request to Purchase Forms (RPF) to its past tense
     */
	function set_rpf_status(string|array $param): string|array
	{
        $options = [
            'pending'   => 'pending',
            'accept'    => 'accepted',
            'accepted'  => 'accepted',
            'reject'    => 'rejected',
            'rejected'  => 'rejected',
            'review'    => 'reviewed',
            'reviewed'  => 'reviewed',
            'receive'   => 'received',
            'received'  => 'received',
        ];

        if (is_array($param)) {
            $arr = [];
            foreach ($param as $val) {
                $arr[] = set_prf_status($val);
            }

            return $arr;
        }

        return $options[$param];
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

if (! function_exists('get_rpf'))
{
    /**
     * Select Reviewed RPF
     */
	function get_rpf(string $param = ''): string|array
	{
        $options = [
            'B' => 'Blue',
            'G' => 'Green',
            'Y' => 'Yellow',
            'O' => 'Orange',
            'R' => 'Red',
        ];

        return $param ? $options[$param] : $options;
	}
}