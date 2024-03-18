<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PRFView extends Seeder
{
    public function run()
    {
        $table  = 'project_request_forms';
        $view   = 'prf_view';
        $join   = 'accounts_view';
        $db     = \Config\Database::connect();

        // Drop if exists
        $db->query("DROP VIEW IF EXISTS {$view}");
        $db->query("DROP TABLE IF EXISTS {$view}");

        // Create
        $db->query("
            CREATE VIEW {$view}
            AS 
            SELECT
                prf.id AS prf_id,
                created.employee_name AS created_by_name,
                (CASE WHEN prf.accepted_by IS NOT NULL THEN accepted.employee_name ELSE prf.accepted_by END) AS accepted_by_name,
                (CASE WHEN prf.rejected_by IS NOT NULL THEN rejected.employee_name ELSE prf.rejected_by END) AS rejected_by_name,
                (CASE WHEN prf.item_out_by IS NOT NULL THEN item_out.employee_name ELSE prf.item_out_by END) AS item_out_by_name,
                (CASE WHEN prf.received_by IS NOT NULL THEN received.employee_name ELSE prf.received_by END) AS received_by_name,
                (CASE WHEN prf.filed_by IS NOT NULL THEN filed.employee_name ELSE prf.filed_by END) AS filed_by_name
            FROM {$table} AS prf
            LEFT JOIN {$join} AS created
                ON created.username = prf.created_by
            LEFT JOIN {$join} AS accepted
                ON accepted.username = prf.accepted_by
            LEFT JOIN {$join} AS rejected
                ON rejected.username = prf.rejected_by
            LEFT JOIN {$join} AS item_out
                ON item_out.username = prf.item_out_by
            LEFT JOIN {$join} AS received
                ON received.username = prf.received_by
            LEFT JOIN {$join} AS filed
                ON filed.username = prf.filed_by
        ");
        
    }
}
