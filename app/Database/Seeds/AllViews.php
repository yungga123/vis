<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AllViews extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        $this->call('EmployeesView');
        $this->call('CustomerView');
        $this->call('TaskleadView');
        $this->call('AccountsView');
        $this->call('TaskleadBookedView');
        $this->call('TaskleadHistoryView');

    }
}
