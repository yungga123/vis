<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateDataInFundsHistoryTable extends Seeder
{
    public function run()
    {
        $table  = 'funds_history';

        // For billing invoice tasklead
        $this->db->query("
            UPDATE 
                `{$table}`
            SET 
                `{$table}`.`coming_from` = 'Billing Invoices (Task/Leads)',
                `{$table}`.`module_code` = 'FINANCE_BILLING_INVOICE'
            WHERE 
                (`{$table}`.`expenses` = '' OR `{$table}`.`expenses` IS NULL)
                AND (`{$table}`.`module_code` = '' OR `{$table}`.`module_code` IS NULL)
                AND UPPER(`{$table}`.`coming_from`) = 'BILLING INVOICE'
        ");

        // For expenses
        $this->db->query("
            UPDATE 
                `{$table}`
            SET 
                `{$table}`.`module_code` = 'FINANCE_FUNDS'
            WHERE 
                (`{$table}`.`expenses` != '' OR `{$table}`.`expenses` IS NOT NULL)
                AND UPPER(`{$table}`.`coming_from`) = 'EXPENSES'
        ");
    }
}
