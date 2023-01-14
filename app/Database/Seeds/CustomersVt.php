<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomersVt extends Seeder
{
    public function run()
    {
        helper('text');
        $db = \Config\Database::connect();
        $table = 'customers_vt';
        $data = [
            [
                'customer_name'   => random_string('basic'),
                'contact_person'   => random_string('basic'),
                'address_province'   => random_string('basic'),
                'address_city'   => random_string('basic'),
                'address_brgy'   => random_string('basic'),
                'address_sub'   => random_string('basic'),
                'contact_number'   => random_string('basic'),
                'email_address'   => random_string('basic'),
                'source'   => random_string('basic'),
                'notes'   => random_string('basic')
            ]
        ];
        $db->table($table)->insertBatch($data);
    }
}
