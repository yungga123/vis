<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerBranchView extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query("
            DROP VIEW IF EXISTS
                customer_view_branch
        ");
        $db->query(
            "CREATE VIEW 
                customer_view_branch 
            AS SELECT
                id,
                customer_id,
                branch_name,
                contact_person,
                contact_number,
                CONCAT_WS(', ',address_province,address_city,address_brgy,address_sub) as address,
                email_address,
                notes,
                deleted_at
            FROM
                customer_branch
            WHERE
                deleted_at IS NULL
            "
        );
    }
}
