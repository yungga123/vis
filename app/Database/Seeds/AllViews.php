<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AllViews extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query("
            DROP VIEW IF EXISTS
                customer_view, employees_view, task_lead, accounts_view
        ");
        $this->call('EmployeesView');
        $this->call('CustomerView');
        $this->call('TaskleadView');
        $this->call('AccountsView');
    }
}
