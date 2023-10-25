<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuppliersView extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query("
            DROP VIEW IF EXISTS
                suppliers_view
        ");

        $db->query(
            "CREATE VIEW 
                suppliers_view 
            AS SELECT
                id,
                supplier_name,
                supplier_type,
                others_supplier_type,
                contact_person,
                contact_number,
                viber,
                payment_terms,
                payment_mode,
                others_payment_mode,
                product,
                remarks,
                email_address,
                bank_name,
                bank_account_name,
                bank_number,
                created_at,
                updated_at,
                deleted_at
            FROM
                suppliers
            WHERE
                deleted_at IS NULL
            "
        );
    }
}
