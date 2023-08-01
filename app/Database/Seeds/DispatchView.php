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
                    ELSE cvtv.customer_name
                END AS customer,
                CASE
                    WHEN d.customer_type = 'residential' 
                    THEN (SELECT contact_person FROM customers_residential_view AS crv WHERE crv.id = d.customer_id)
                    ELSE cvtv.contact_person
                END AS contact_person,
                CASE
                    WHEN d.customer_type = 'residential' 
                    THEN (SELECT contact_number FROM customers_residential_view AS crv WHERE crv.id = d.customer_id)
                    ELSE cvtv.contact_number
                END AS contact_number,
                CASE
                    WHEN d.customer_type = 'residential' 
                    THEN (SELECT email_address FROM customers_residential_view AS crv WHERE crv.id = d.customer_id)
                    ELSE cvtv.email_address
                END AS email_address,
                CASE
                    WHEN d.customer_type = 'residential' 
                    THEN (SELECT address FROM customers_residential_view AS crv WHERE crv.id = d.customer_id)
                    ELSE cvtv.address
                END AS address,
                GROUP_CONCAT(em.employee_id) AS technician_ids, 
                GROUP_CONCAT(em.firstname, ' ', em.lastname) AS technicians, 
                GROUP_CONCAT('{$span}', em.firstname, ' ', em.lastname, '</span>' SEPARATOR ' ') AS technicians_formatted,
                av.employee_name AS dispatch_by,
                av.access_level AS dispatch_by_role
            FROM dispatch AS d
            LEFT JOIN dispatched_technicians AS dt
                ON dt.dispatch_id = d.id
            JOIN employees AS em 
                ON dt.employee_id = em.employee_id
            LEFT JOIN customervt_view AS cvtv
                ON cvtv.id = d.customer_id
            LEFT JOIN accounts_view AS av
                ON av.username = d.created_by
            GROUP BY d.id)
        ");
    }
}
