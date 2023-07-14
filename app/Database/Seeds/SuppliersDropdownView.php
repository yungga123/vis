<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuppliersDropdownView extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query("
            DROP VIEW IF EXISTS
                suppliers_dropdown_view
        ");

        $db->query(
            "CREATE VIEW 
                suppliers_dropdown_view 
            AS SELECT
                id,
                dropdown,
                dropdown_type
            FROM
                suppliers_dropdown
            WHERE
                deleted_at IS NULL
            "
        );
    }
}
