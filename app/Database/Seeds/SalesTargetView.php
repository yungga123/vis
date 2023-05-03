<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SalesTarget extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query("
            DROP VIEW IF EXISTS
                sales_target_view
        ");
        $db->query(
            "CREATE VIEW 
                sales_target_view 
            AS SELECT
                sales_target.id as id,
                sales_id,
                CONCAT(firstname,' ',middlename,' ',lastname) AS employee_name,
                q1_target,
                q2_target,
                q3_target,
                q4_target
            FROM
                sales_target
            LEFT JOIN
                employees
            ON
                sales_target.sales_id=employees.employee_id
            WHERE
                sales_target.deleted_at IS NULL
            "
        );
    }
}
