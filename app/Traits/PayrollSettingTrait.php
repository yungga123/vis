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
                $this->formatResult($model->fetchAll($param)) 
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
            'default_vacation_leave',
            'default_sick_leave',
            'default_emergency_leave',
            'default_other_leave',
        ];
        $leave = $this->getPayrollSettings($params, true);

        return $leave;
    }

    /**
     * Get overtimes, night diff and holiday's percentage rate
     * 
     * @return array|null
     */
    public function getOvertimeRates()
    {
        $params = [
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

        return $overtimes;
    }

    /**
     * Get government's percentage rate
     * 
     * @return array|null
     */
    public function getGovtRates()
    {
        $params = [
            'sss_contri_rate_employee',
            'sss_contri_rate_employeer',
            'sss_salary_range_min',
            'sss_salary_range_max',
            'sss_starting_msc',
            'sss_last_msc',
            'sss_next_diff_amount',
            'pagibig_contri_rate_employee',
            'pagibig_contri_rate_employeer',
            'philhealth_contri_rate',
        ];
        $govtRates = $this->getPayrollSettings($params, true);

        return $govtRates;
    }

    /**
     * Get BIR tax rate table
     * 
     * @return array|null
     */
    public function getBirTaxTable()
    {
        $model  = new BirTaxModel;
        $result = $model->fetchAll();

        return $result;
    }

    /**
     * Formatting the result into one assoc array
     *
     * @param array $result     The array/result to format
     * @return array            The formatted array
     */
    public function formatResult($result) 
    {
        $arr = [];

        if (! empty($result)) {
            foreach ($result as $key => $value) {
                $arr[$value['key']] = $value['value'];
            }
        }

        return $arr;
    }
}