<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AccountsView extends Seeder
{
    public function run()
    {
        $this->db->query("
            DROP VIEW IF EXISTS
                accounts_view
        ");
        $this->db->query(
            "CREATE VIEW 
                accounts_view 
            AS SELECT
                accounts.account_id AS id,
                accounts.employee_id AS employee_id,
                CONCAT(employees.firstname,' ',employees.lastname) AS employee_name,
                accounts.username,
                accounts.password,
                accounts.access_level,
                accounts.profile_img,
                CONCAT(emp.firstname,' ',emp.lastname) AS created_by_name
            FROM
                accounts
            LEFT JOIN
                employees
            ON
                accounts.employee_id = employees.employee_id
            LEFT JOIN
                employees AS emp
            ON
                accounts.created_by = emp.employee_id
            WHERE
                accounts.deleted_at IS NULL
            "
        );
    }
}
