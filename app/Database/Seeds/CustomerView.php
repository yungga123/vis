<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerView extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query(
            "CREATE VIEW customer_view AS SELECT
                id,
                customer_name,
                contact_person,
                CONCAT_WS(', ',address_province,address_city,address_brgy,address_sub) as address,
                contact_number,
                email_address,
                source,
                notes,
                deleted_at
                FROM
                customers
                WHERE
                deleted_at IS NULL
            "
        );
    }
}
