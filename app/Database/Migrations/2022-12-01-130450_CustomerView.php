<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;


class CustomerView extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $db->query(
            "CREATE VIEW customer_view AS SELECT
                id,
                customer_name,
                contact_person,
                CONCAT_WS(' ,',address_province,address_city,address_brgy,address_sub) as address,
                contact_number,
                email_address,
                source,
                notes
                FROM
                customers
            "
        );
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query("DROP VIEW customer_view");
    }
}
