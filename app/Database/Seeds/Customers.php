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
            ],
            [
                'customer_name'   => "Jardin Fine Dinings Inc.",
                'contact_person'   => "MR. REYNAN",
                'address_province'   => "Metro Manila",
                'address_city'   => "Muntinlupa City",
                'address_brgy'   => "Putatan",
                'address_sub'   => "South Greenheights Village",
                'contact_number'   => "09995609253",
                'email_address'   => "yungga321@gmail.com",
                'source'   => "Twitter, Referral",
                'notes'   => "Angel's Property"
            ],
            [
                'customer_name'   => "Isaac Holdings Corp.",
                'contact_person'   => "Mr. John Isaac Mallanes",
                'address_province'   => "Metro Manila",
                'address_city'   => "Muntinlupa City",
                'address_brgy'   => "Poblacion",
                'address_sub'   => "Katarungan Village",
                'contact_number'   => "09595656854",
                'email_address'   => "isaac@gmail.com",
                'source'   => "Code Z",
                'notes'   => "Somebody help me pls"
            ]
        ];
        $db->table($table)->insertBatch($data);
    }
}
