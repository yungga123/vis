<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AllViews extends Seeder
{
    public function run()
    {
        $this->call('AccountsView');
        $this->call('EmployeesView');
        $this->call('TaskleadBookedView');
        $this->call('TaskleadHistoryView');
        $this->call('TaskleadView');
        //$this->call('TaskleadViewExistingCustomer');
        $this->call('SalesTargetView');
        $this->call('SuppliersView');
        $this->call('SuppliersBrandView');
        $this->call('DispatchView');
        $this->call('Roles');
        $this->call('InventoryView');
        $this->call('PRFView');
        $this->call('RPFView');
        $this->call('CustomersVTToCustomersDataTransfer');
        $this->call('CustomersVTBranchToCustomerBranchessDataTransfer');
        $this->call('DropViews');
        $this->call('DeleteClientsPermissions');
    }
}
