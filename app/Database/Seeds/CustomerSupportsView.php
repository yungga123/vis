<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerSupportsView extends Seeder
{
    public function run()
    {
        $table  = 'customer_supports';
        $view   = 'customer_supports_view';
        $span   = '<span class="td-specialists">';

        // Drop if exists
        $this->db->query("DROP VIEW IF EXISTS {$view}");
        $this->db->query("DROP TABLE IF EXISTS {$view}");

        // Create
        $this->db->query("
            CREATE VIEW {$view}
            AS 
            (SELECT
                {$table}.id AS customer_support_id,
                GROUP_CONCAT(em.employee_id) AS specialist_ids, 
                GROUP_CONCAT(em.firstname, ' ', em.lastname) AS specialists, 
                GROUP_CONCAT('{$span}', em.firstname, ' ', em.lastname, '</span>' SEPARATOR ' ') AS specialists_formatted,
                {$table}.customer_id,
                customers.name AS client_name,
                customers.type AS customer_type,
                {$table}.customer_branch_id,
                customer_branches.branch_name AS client_branch_name,
                cb.employee_name AS created_by,
                db.employee_name AS done_by,
                tob.employee_name AS turn_over_by
            FROM {$table}
            LEFT JOIN customer_support_specialists AS css
                ON css.customer_support_id = {$table}.id
            LEFT JOIN employees AS em 
                ON css.employee_id = em.employee_id
            LEFT JOIN customers 
                ON {$table}.customer_id = customers.id
            LEFT JOIN customer_branches 
                ON ({$table}.customer_branch_id = customer_branches.id AND {$table}.customer_branch_id IS NOT NULL)
            LEFT JOIN accounts_view AS cb
                ON cb.username = {$table}.created_by
            LEFT JOIN accounts_view AS db
                ON db.username = {$table}.done_by
            LEFT JOIN accounts_view AS tob
                ON tob.username = {$table}.turn_over_by
            GROUP BY {$table}.id)
        ");
    }
}
