<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AllViews extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        $this->call('AccountsView');
        $this->call('CustomerBranchView');
        $this->call('CustomerView');
        $this->call('EmployeesView');
        $this->call('TaskleadBookedView');
        $this->call('TaskleadHistoryView');
        $this->call('TaskleadView');
        $this->call('CustomerVtView');
        $this->call('CustomerVtBranchView');

    }
}
