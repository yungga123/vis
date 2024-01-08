<?php

namespace App\Traits;

use App\Models\BirTaxModel;
use App\Models\PayrollSettingModel;

trait PayrollSettingTrait
{
    /**
     * Fetching the general info
     *
     * @param string|array|null $param  The param to search
     * @param bool $format              Whether to format the result
     * @return array|string|null        The results of the search
     */
    public function getPayrollSettings($param = [], $format = false)
    {
        $model = new PayrollSettingModel();

        if(is_array($param)) {            
            return $format ? 
                format_results($model->fetchAll($param)) 
                : $model->fetchAll($param);
        }
        
        $result = $model->fetch($param);
        
        return $result ? $result['value'] : null;
    }

    /**
     * Get office hours
     *
     * @param bool $format     Whether to format return
     * 
     * @return array|string
     */
    public function getOfficeHours($format = false)
    {
        $result         = '';
        $office_hours   = $this->getPayrollSettings(['working_time_in', 'working_time_out'], true);

        if (! empty($office_hours)) {
            $time_in    = format_time($office_hours['working_time_in']);
            $time_out   = format_time($office_hours['working_time_out']);

            if ($format) {
                $result = "{$time_in} to {$time_out}";
            }
        }

        return $format ? $result : $office_hours;
    }

    /**
     * Get company's working days
     * 
     * @return array|null
     */
    public function getWorkingDays()
    {
        $working_days = $this->getPayrollSettings('working_days', true);

        return $working_days;
    }

    /**
     * Get default leave counts
     * 
     * @return array|null
     */
    public function getDefaultLeaveCounts()
    {
        $params = [
            'default_service_incentive_leave',
            'default_emergency_leave',
            'default_other_leave',
        ];
        $leave = $this->getPayrollSettings($params, true);

        return $leave;
    }

    /**
     * Get overtimes, night diff and holiday's percentage rate
     * 
     * @param bool $convert     Whether to convert percent to decimal
     * 
     * @return array|null
     */
    public function getOvertimeHolidayRates($convert = false)
    {
        $arr        = [];
        $params     = [
            'overtime',
            'night_diff',
            'rest_day',
            'rest_day_overtime',
            'regular_holiday',
            'regular_holiday_overtime',
            'special_holiday',
            'special_holiday_overtime',
        ];
        $overtimes = $this->getPayrollSettings($params, true);

        foreach ($params as $key => $val) {
            $param      = floatval($overtimes[$val] ?? 0);
            $arr[$val]  = floatval($convert ? $param / 100 : $param);
            // $arr[$val]  = floatval(number_format($convert ? $param / 100 : $param, 2));
        }

        return $arr;
    }

    /**
     * Get government's percentage rate
     * 
     * @param bool $convert     Whether to convert percent to decimal
     * 
     * @return array|null
     */
    public function getGovtRates($convert = false)
    {
        $params     = [
            'sss_contri_rate_employee',
            'sss_contri_rate_employer',
            'sss_salary_range_min',
            'sss_salary_range_max',
            'sss_next_diff_range_start_amount',
            'sss_starting_msc',
            'sss_last_msc',
            'sss_next_diff_msc_total_amount',
            'pagibig_contri_rate_employee',
            'pagibig_contri_rate_employer',
            'pagibig_max_monthly_contri',
            'philhealth_contri_rate',
            'philhealth_income_floor',
            'philhealth_if_monthly_premium',
            'philhealth_income_ceiling',
            'philhealth_ic_monthly_premium',
        ];
        $rates  = $this->getPayrollSettings($params, true);

        if (! $convert) return $rates;

        $params     = [
            'sss_contri_rate_employee'      => floatval(floatval($rates['sss_contri_rate_employee'] ?? 0) / 100),
            'sss_contri_rate_employer'      => floatval(floatval($rates['sss_contri_rate_employer'] ?? 0) / 100),
            'sss_salary_range_min'          => floatval($rates['sss_salary_range_min'] ?? 0),
            'sss_salary_range_max'          => floatval($rates['sss_salary_range_max'] ?? 0),
            'sss_next_diff_range_start_amount' => floatval($rates['sss_next_diff_range_start_amount'] ?? 0),
            'sss_starting_msc'              => floatval($rates['sss_starting_msc'] ?? 0),
            'sss_last_msc'                  => floatval($rates['sss_last_msc'] ?? 0),
            'sss_next_diff_msc_total_amount' => floatval($rates['sss_next_diff_msc_total_amount'] ?? 0),
            'pagibig_contri_rate_employee'  => floatval(floatval($rates['pagibig_contri_rate_employee'] ?? 0) / 100),
            'pagibig_contri_rate_employer'  => floatval(floatval($rates['pagibig_contri_rate_employer'] ?? 0) / 100),
            'pagibig_max_monthly_contri'    => floatval($rates['pagibig_max_monthly_contri'] ?? 200),
            'philhealth_contri_rate'        => floatval(floatval($rates['philhealth_contri_rate'] ?? 0) / 100),
            'philhealth_income_floor'       => floatval($rates['philhealth_income_floor'] ?? 0),
            'philhealth_if_monthly_premium' => floatval($rates['philhealth_if_monthly_premium'] ?? 0),
            'philhealth_income_ceiling'     => floatval($rates['philhealth_income_ceiling'] ?? 0),
            'philhealth_ic_monthly_premium' => floatval($rates['philhealth_ic_monthly_premium'] ?? 0),
        ];

        return $params;
    }

    /**
     * Get government's SSS percentage rate
     * 
     * @param bool $convert     Whether to convert percent to decimal
     * 
     * @return array|null
     */
    public function getGovtSSSRates($convert = false)
    {
        $params = [
            'sss_contri_rate_employee',
            'sss_contri_rate_employer',
            'sss_salary_range_min',
            'sss_salary_range_max',
            'sss_next_diff_range_start_amount',
            'sss_starting_msc',
            'sss_last_msc',
            'sss_next_diff_msc_total_amount',
        ];
        $rates  = $this->getPayrollSettings($params, true);

        if (! $convert) return $rates;

        $params     = [
            'sss_contri_rate_employee'      => floatval(floatval($rates['sss_contri_rate_employee'] ?? 0) / 100),
            'sss_contri_rate_employer'      => floatval(floatval($rates['sss_contri_rate_employer'] ?? 0) / 100),
            'sss_salary_range_min'          => floatval($rates['sss_salary_range_min'] ?? 0),
            'sss_salary_range_max'          => floatval($rates['sss_salary_range_max'] ?? 0),
            'sss_next_diff_range_start_amount' => floatval($rates['sss_next_diff_range_start_amount'] ?? 0),
            'sss_starting_msc'              => floatval($rates['sss_starting_msc'] ?? 0),
            'sss_last_msc'                  => floatval($rates['sss_last_msc'] ?? 0),
            'sss_next_diff_msc_total_amount' => floatval($rates['sss_next_diff_msc_total_amount'] ?? 0),
        ];

        return $params;
    }

    /**
     * Get government's PAG-IBIG percentage rate
     * 
     * @param bool $convert     Whether to convert percent to decimal
     * 
     * @return array|null
     */
    public function getGovtPagibigRates($convert = false)
    {
        $params = [
            'pagibig_contri_rate_employee',
            'pagibig_contri_rate_employer',
            'pagibig_max_monthly_contri',
        ];
        $rates  = $this->getPayrollSettings($params, true);

        if (! $convert) return $rates;

        $params     = [
            'pagibig_contri_rate_employee'  => floatval(floatval($rates['pagibig_contri_rate_employee'] ?? 0) / 100),
            'pagibig_contri_rate_employer'  => floatval(floatval($rates['pagibig_contri_rate_employer'] ?? 0) / 100),
            'pagibig_max_monthly_contri'    => floatval($rates['pagibig_max_monthly_contri'] ?? 200),
        ];

        return $params;
    }

    /**
     * Get government's percentage rate
     * 
     * @param bool $convert     Whether to convert percent to decimal
     * 
     * @return array|null
     */
    public function getGovtPhilhealthRates($convert = false)
    {
        $params = [
            'philhealth_contri_rate',
            'philhealth_income_floor',
            'philhealth_if_monthly_premium',
            'philhealth_income_ceiling',
            'philhealth_ic_monthly_premium',
        ];
        $rates  = $this->getPayrollSettings($params, true);

        if (! $convert) return $rates;

        $params     = [
            'philhealth_contri_rate'        => floatval(floatval($rates['philhealth_contri_rate'] ?? 0) / 100),
            'philhealth_income_floor'       => floatval($rates['philhealth_income_floor'] ?? 0),
            'philhealth_if_monthly_premium' => floatval($rates['philhealth_if_monthly_premium'] ?? 0),
            'philhealth_income_ceiling'     => floatval($rates['philhealth_income_ceiling'] ?? 0),
            'philhealth_ic_monthly_premium' => floatval($rates['philhealth_ic_monthly_premium'] ?? 0),
        ];

        return $params;
    }

    /**
     * Get BIR tax rate table
     * 
     * @param bool $format     Whether to format results
     * 
     * @return array|null
     */
    public function getBirTaxTable($format = false)
    {
        $arr    = [];
        $model  = new BirTaxModel;
        $result = $model->fetchAll();

        if (! $format) return $result;

        if (! empty($result)) {
            foreach ($result as $key => $val) {
                $connector  = $val['below_or_above'] ? '_and_'. $val['below_or_above'] : '_to_' . $val['compensation_range_end'];
                $_key       = $val['compensation_range_start'] . $connector;
                $arr[$_key] = $val;
            }
        }

        return $arr;
    }
}