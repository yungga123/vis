<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AccountsView extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query(
            "CREATE VIEW 
                accounts_view 
            AS SELECT
                accounts.employee_id as employee_id,
                CONCAT(employees.firstname,' ',employees.lastname) as employee_name,
                username,
                password,
                access_level
            FROM
                accounts
            LEFT JOIN
                employees
            ON
                accounts.employee_id = employees.employee_id
            WHERE
                accounts.deleted_at IS NULL
            "
        );
    }
}
