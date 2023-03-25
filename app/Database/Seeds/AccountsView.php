<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AccountsView extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query("
            DROP VIEW IF EXISTS
                accounts_view
        ");
        $db->query(
            "CREATE VIEW 
                accounts_view 
            AS SELECT
                accounts.account_id as id,
                accounts.employee_id as employee_id,
                CONCAT(employees.firstname,' ',employees.lastname) as employee_name,
                username,
                password,
                access_level,
                profile_img
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
