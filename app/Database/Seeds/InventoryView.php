<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InventoryView extends Seeder
{
    public function run()
    {
        $table  = 'inventory';
        $view   = 'inventory_view';
        $join   = 'inventory_dropdowns';
        $db     = \Config\Database::connect();

        // Drop if exists
        $db->query("DROP VIEW IF EXISTS {$view}");
        $db->query("DROP TABLE IF EXISTS {$view}");

        // Create
        $db->query("
            CREATE VIEW {$view}
            AS 
            (SELECT
                inv.id AS inventory_id,
                TRIM(BOTH '\r\n' FROM cat.dropdown) AS category_name,
                TRIM(BOTH '\r\n' FROM subcat.dropdown) AS subcategory_name,
                TRIM(BOTH '\r\n' FROM brand.dropdown) AS brand,
                TRIM(BOTH '\r\n' FROM size.dropdown) AS size,
                TRIM(BOTH '\r\n' FROM unit.dropdown) AS unit,
                av.employee_name AS encoder_name
            FROM {$table} AS inv
            LEFT JOIN {$join} AS cat
                ON cat.dropdown_id = inv.category
            LEFT JOIN {$join} AS subcat
                ON subcat.dropdown_id = inv.sub_category
            LEFT JOIN {$join} AS brand
                ON brand.dropdown_id = inv.item_brand
            LEFT JOIN {$join} AS size
                ON size.dropdown_id = inv.category
            LEFT JOIN {$join} AS unit
                ON unit.dropdown_id = inv.stock_unit
            LEFT JOIN accounts_view AS av
                ON av.employee_id = inv.encoder)
        ");
    }
}
