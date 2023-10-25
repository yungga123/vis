<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DropViews extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query("DROP VIEW IF EXISTS customers_residential_view");
        $db->query("DROP VIEW IF EXISTS customer_view");
        $db->query("DROP VIEW IF EXISTS customer_view_branch");
        $db->query("DROP VIEW IF EXISTS customervt_view");
        $db->query("DROP VIEW IF EXISTS customervt_view_branch");

        // Drop Tables
        $db->query("DROP TABLE IF EXISTS customers_vt");
        $db->query("DROP TABLE IF EXISTS customervt_branch");
    }
}
