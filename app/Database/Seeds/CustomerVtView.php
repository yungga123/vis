<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerVtView extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query("
            DROP VIEW IF EXISTS
                customervt_view
        ");
        $db->query(
            "CREATE VIEW 
                customervt_view 
            AS SELECT
                id,
                if(forecast=1,'YES','NO') AS forecast,
                customer_name,
                contact_person,
                CONCAT_WS(', ',address_province,address_city,address_brgy,address_sub) as address,
                contact_number,
                email_address,
                source,
                notes,
                referred_by,
                customer_type,
                deleted_at
            FROM
                customers_vt
            WHERE
                deleted_at IS NULL
            "
        );
    }
}
