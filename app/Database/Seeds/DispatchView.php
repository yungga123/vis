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
                GROUP_CONCAT(em.employee_id) AS technician_ids, 
                GROUP_CONCAT(em.firstname, ' ', em.lastname) AS technicians, 
                GROUP_CONCAT('{$span}', em.firstname, ' ', em.lastname, '</span>' SEPARATOR ' ') AS technicians_formatted,
                av.employee_name AS dispatched_by,
                av.access_level AS dispatched_by_role,
                (SELECT emp2.position FROM employees AS emp2 WHERE emp2.employee_id = av.employee_id) AS dispatched_by_position,
                CONCAT(em1.firstname, ' ', em1.lastname) AS checked_by_name,
                em1.position AS checked_by_position
            FROM dispatch AS d
            LEFT JOIN dispatched_technicians AS dt
                ON dt.dispatch_id = d.id
            JOIN employees AS em 
                ON dt.employee_id = em.employee_id
            LEFT JOIN accounts_view AS av
                ON av.username = d.created_by
            LEFT JOIN employees AS em1 
                ON d.checked_by = em1.employee_id
            GROUP BY d.id)
        ");
    }
}
