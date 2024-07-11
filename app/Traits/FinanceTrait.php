<?php

namespace App\Traits;

use App\Models\FundsHistoryModel;

trait FinanceTrait
{
    /* Declare trait here to use */
    use GeneralInfoTrait;

    /**
     * Get overdue interests
     *
     * @return array    The results
     */
    public function overdueInterests()
    {
        $keys   = [
            'billing_invoice_overdue_interest_per_day',
            'billing_invoice_overdue_interest_per_month',
        ];
        $arr    = $this->getGeneralInfo($keys, true);
        $arr    = [
            'per_day'   => (isset($arr[$keys[0]]) && $arr[$keys[0]] ? $arr[$keys[0]] : 0.23) / 100,
            'per_month' => (isset($arr[$keys[1]]) && $arr[$keys[1]] ? $arr[$keys[1]] : 7) / 100,
        ];

        return $arr;
    }

    /**
     * Get overdue interests
     *
     * @param string|int $billing_amount    The amount to check and calculate
     * @param string $overdue_date          The overdue date
     * @param string|null $compare_to       The date to compare to - default current date
     * 
     * @return array                        The results
     */
    public function checkNCalculateOverdues($billing_amount, $overdue_date, $compare_to = null)
    {
        $arr        = [];
        $compare_to ??= current_date();

        if (compare_dates($overdue_date, $compare_to, '<')) {
            $interval       = get_date_diff($overdue_date, $compare_to);
            $days_overdue   = $interval->days;
            $interest       = $this->overdueInterests()['per_day'];

            $arr['days']    = $days_overdue;
            $arr['amount']  = $billing_amount * $interest;
        }

        return $arr;
    }

    /**
     * Save funds transaction history
     *
     * @param array $data  The data to save
     * 
     * @return void
     */
    public function saveFundTransaction($data)
    {
        if (!empty($data)) {
            $fundHModel = new FundsHistoryModel();
            $params     = [
                'billing_invoice_id'    => $data['billing_invoice_id'] ?? $data['id'] ?? 0,
                'current_funds'         => $data['current_funds'] ?? $this->getCompanyFunds(),
                'transaction_amount'    => $data['transaction_amount'],
                'transaction_type'      => $data['transaction_type'] ?? 'incoming',
                'coming_from'           => $data['coming_from'],
                'module_code'           => $data['module_code'],
            ];

            $fundHModel->save($params);
        }
    }
}
