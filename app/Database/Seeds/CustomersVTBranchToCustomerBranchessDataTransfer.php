<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomersVTBranchToCustomerBranchessDataTransfer extends Seeder
{
    public function run()
    {
        $db     = \Config\Database::connect();
        $from   = 'customervt_branch';
        $to     = 'customer_branches';

        $db->query("
            INSERT INTO {$to} 
                (id, customer_id, branch_name, province, city, barangay, subdivision,
                contact_person, contact_number, email_address, notes,
                created_by, created_at, updated_at, deleted_at)
            (SELECT 
                id, customer_name, address_province, address_city, IF(TRIM(UPPER(address_brgy)) = 'N/A', '', address_brgy) AS address_brgy, address_sub,
                contact_person, contact_number, email_address, notes,
                'yungga', created_at, updated_at, deleted_at
            FROM 
                {$from})
        ");
    }
}
