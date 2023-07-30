<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DispatchView extends Seeder
{
    public function run()
    {
        $view   = 'dispatch_view';
        $span   = '<span class="td-technicians">';
        $db     = \Config\Database::connect();

        // Drop if exists
        $db->query("DROP VIEW IF EXISTS {$view}");
        $db->query("DROP TABLE IF EXISTS {$view}");

        // Create
        $db->query("
            CREATE VIEW {$view}
            AS 
            (SELECT
                d.id AS dispatch_id,
                CASE
                    WHEN d.customer_type = 'residential' 
                    THEN (SELECT customer_name FROM customers_residential AS cr WHERE cr.id = d.customer_id)
                    ELSE (SELECT customer_name FROM customers_vt AS cvt WHERE cvt.id = d.customer_id)
                END AS customer,
                GROUP_CONCAT(em.employee_id) AS technician_ids, 
                GROUP_CONCAT(em.firstname, ' ', em.lastname) AS technicians, 
                GROUP_CONCAT('{$span}', em.firstname, ' ', em.lastname, '</span>' SEPARATOR ' ') AS technicians_formatted
            FROM dispatch AS d
            LEFT JOIN dispatched_technicians AS dt
                ON dt.dispatch_id = d.id
            JOIN employees AS em 
                ON dt.employee_id = em.employee_id
            GROUP BY d.id)
        ");
    }
}
