<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomersVTToCustomersDataTransfer extends Seeder
{
    public function run()
    {
        $db     = \Config\Database::connect();
        $from   = 'customers_vt';
        $to     = 'customers';

        if ($db->tableExists($from)) {
            $db->query("
                INSERT INTO {$to} 
                    (id, `name`, province, city, barangay, subdivision,
                    contact_person, contact_number, email_address, 
                    `type`, forecast, `source`, notes, referred_by,
                    created_by, created_at, updated_at, deleted_at)
                (SELECT 
                    id, customer_name, address_province, address_city, IF(TRIM(UPPER(address_brgy)) = 'N/A', '', address_brgy) AS address_brgy, address_sub,
                    contact_person, contact_number, email_address, 
                    UPPER(customer_type), forecast, `source`, notes, referred_by,
                    'yungga', created_at, updated_at, deleted_at
                FROM 
                    {$from})
            ");
        }
    }
}