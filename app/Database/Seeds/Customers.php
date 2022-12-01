<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Customers extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $table = 'customers';
        $data = [
            [
                'customer_name'   => "Angelica Zaldivar Holdings Inc.",
                'contact_person'   => "MS. ANGELICA",
                'address_province'   => "Cavite",
                'address_city'   => "Cavity City",
                'address_brgy'   => "Dalahican",
                'address_sub'   => "143",
                'contact_number'   => "09994835734",
                'email_address'   => "angelica@gmail.com",
                'source'   => "FB",
                'notes'   => "Reynan's Property"
            ]
        ];
        $db->table($table)->insertBatch($data);
    }
}
