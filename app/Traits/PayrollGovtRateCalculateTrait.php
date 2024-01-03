<?php

namespace App\Traits;

trait PayrollGovtRateCalculateTrait
{
    use PayrollSettingTrait;

    /**
     * Calculate SSS contributions
     *
     * @param int|float|string $income  The income/salary to calculate
     * @param array $rates              Optional - can pass the SSS rates
     * @param bool $is_employeer        Whether to get employeer's contribution
     * 
     * @return float
     */
    public function calculateSSSContri($income, $rates = [], $is_employeer = false)
    {
        // Get SSS rates
        $rates = empty($rates) ? $this->getGovtSSSRates(true) : $rates;

        if (empty($rates)) return 0;

        $starting_salary_range  = $rates['sss_salary_range_min'];
        $last_salary_range      = $rates['sss_salary_range_max'];
        $starting_msc_total     = $rates['sss_starting_msc'];
        $last_msc_total         = $rates['sss_last_msc'];
        $next_diff_salary_range = $rates['sss_next_diff_range_start_amount'];
        $next_diff_msc_amount   = $rates['sss_next_diff_msc_total_amount'];
        $employee_share_rate    = $rates['sss_contri_rate_employee'];
        $employeer_share_rate   = $rates['sss_contri_rate_employeer'];

        $count      = 0;
        $result     = 0;
        $share_rate = $is_employeer ? $employeer_share_rate : $employee_share_rate;
        $next_starting_salary_range = $starting_salary_range;
        $opposite_salary_amount     = $starting_salary_range + $next_diff_salary_range;
        $next_msc_total_amount      = $starting_msc_total + $next_diff_msc_amount;

        while ($count <= 50) {
            if ($income < $starting_salary_range && $count === 0) {
                $result = $starting_msc_total * $share_rate;
                break;
            }

            if ($income > $last_salary_range) {
                $result = $last_msc_total * $share_rate;
                break;
            }

            if (
                $income >= $next_starting_salary_range &&
                $income <= $opposite_salary_amount
            ) {
                $result = $next_msc_total_amount * $share_rate;
                break;
            }

            $next_starting_salary_range += $next_diff_msc_amount;
            $opposite_salary_amount += $next_diff_msc_amount;
            $next_msc_total_amount += $next_diff_msc_amount;
            $count++;
        }

        return $result;
    }

    /**
     * Calculate Pag-IBIG contributions
     *
     * @param int|float|string $income  The income/salary to calculate
     * @param array $rates              Optional - can pass the Pag-IBIG rates
     * @param bool $is_employeer        Whether to get employeer's contribution
     * 
     * @return float
     */
    public function calculatePagibigContri($income, $rates = [], $is_employeer = false)
    {
        // Get Pag-IBIG rates
        $result         = 0;
        $rates          = empty($rates) ? $this->getGovtPagibigRates(true) : $rates;

        if (empty($rates)) return 0;

        $employee_rate  = $rates['pagibig_contri_rate_employee'];
        $employeer_rate = $rates['pagibig_contri_rate_employeer'];
        $max_monthly    = $rates['pagibig_max_monthly_contri'];
        $share_rate     = $is_employeer ? $employeer_rate : $employee_rate;
        // Income multiply by rate
        $result         = ($income * $share_rate);
        // If result is greater than max monthly, get it
        $result         = $result > $max_monthly ? $max_monthly : $result;
         // Divided by 2 (employee and employeer)
        $result         = $result / 2;

        return round($result, 2);
    }

    /**
     * Calculate PhilHealth contributions
     *
     * @param int|float|string $income  The income/salary to calculate
     * @param array $rates              Optional - can pass the PhilHealth rate
     * @param bool $monthly             Whether to calculate monthly income
     * 
     * @return float
     */
    public function calculatePhilhealthContri($income, $rates = [], $monthly = false)
    {
        // Get PhilHealth rates
        $result     = 0;
        $rates      = empty($rates) ? $this->getGovtPhilhealthRates(true) : $rates;

        if (empty($rates)) return 0;

        $share_rate         = $rates['philhealth_contri_rate'];
        $income_floor       = $rates['philhealth_income_floor'];
        $if_monthly_premium = $rates['philhealth_if_monthly_premium'];
        $income_ceiling     = $rates['philhealth_income_ceiling'];
        $if_monthly_premium = $rates['philhealth_ic_monthly_premium'];

        // Multiply by 2 if semi-monthly
        $income     = $monthly ? $income : $income * 2;
        // Income multiply by rate
        $result     = ($income * $share_rate);

        // If less than or equal to income floor
        if ($income <= $income_floor) {
            $result = $if_monthly_premium;
        }
        
        // If greater than or equal to income ceiling
        if ($income >= $income_ceiling) {
            $result = $if_monthly_premium;
        }

         // Divided by 2 (employee and employeer)
        $result     = $result / 2;

        // If semi-monthly divided by 2
        return round($monthly ? $result : $result / 2, 2);
    }

    /**
     * Calculate Withholding Tax
     *
     * @param int|float|string $income  The income/salary to calculate
     * @param bool $monthly             Whether to calculate monthly income
     * 
     * @return float|string
     */
    public function calculateWithholdingTax($income = 0, $monthly = false)
    {
        // Get BIR Tax list
        $result     = 0;
        $list       = $this->getBirTaxTable(true);

        if (empty($list)) return 0;

        // Loop through the list
        foreach (array_values($list) as $key => $val) {
            if (! $monthly) {
                // If semi-monthly, divided by 2
                $val['compensation_range_start']    = round($val['compensation_range_start'] / 2);
                $val['compensation_range_end']      = round($val['compensation_range_end'] / 2);
                $val['fixed_tax_amount']            = $val['fixed_tax_amount'] / 2;
            }

            $start_range    = $val['compensation_range_start'];
            $end_range      = $val['compensation_range_end'];
            $fixed_amt      = $val['fixed_tax_amount'];
            $tax_rate       = $val['tax_rate'] / 100;

            // Check if income is below the min start range
            $is_below       = $val['below_or_above'] === 'below' && $income <= $start_range;
            // Then, return 0 - not taxable
            if ($is_below) return 0;

            $is_above       = $val['below_or_above'] === 'above' && $income >= $start_range;
            $is_middle      = $income >= $start_range && $income <= $end_range;

            if ($is_above || $is_middle) {
                $result = $fixed_amt + ($income - $start_range);
                $result = $result * $tax_rate;

                return round($result, 2);
            }
        }
    }
}