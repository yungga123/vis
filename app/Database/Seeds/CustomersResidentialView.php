<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomersResidentialView extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $db->query("
            DROP VIEW IF EXISTS
                customers_residential_view
        ");

        $db->query(
            "CREATE VIEW 
                customers_residential_view 
            AS SELECT
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
                customers_residential
            WHERE
                deleted_at IS NULL
            "
        );
    }
}
