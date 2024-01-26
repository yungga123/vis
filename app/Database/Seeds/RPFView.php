<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RPFView extends Seeder
{
    public function run()
    {
        $table  = 'request_purchase_forms';
        $view   = 'rpf_view';
        $join   = 'accounts_view';

        // Drop if exists
        $this->db->query("DROP VIEW IF EXISTS {$view}");
        $this->db->query("DROP TABLE IF EXISTS {$view}");

        // Create
        $this->db->query("
            CREATE VIEW {$view}
            AS 
            SELECT
                rpf.id AS rpf_id,
                created.employee_name AS created_by_name,
                (CASE WHEN rpf.accepted_by IS NOT NULL THEN accepted.employee_name ELSE rpf.accepted_by END) AS accepted_by_name,
                (CASE WHEN rpf.rejected_by IS NOT NULL THEN rejected.employee_name ELSE rpf.rejected_by END) AS rejected_by_name,
                (CASE WHEN rpf.reviewed_by IS NOT NULL THEN reviewed.employee_name ELSE rpf.reviewed_by END) AS reviewed_by_name
            FROM {$table} AS rpf
            LEFT JOIN {$join} AS created
                ON created.username = rpf.created_by
            LEFT JOIN {$join} AS accepted
                ON accepted.username = rpf.accepted_by
            LEFT JOIN {$join} AS rejected
                ON rejected.username = rpf.rejected_by
            LEFT JOIN {$join} AS reviewed
                ON reviewed.username = rpf.reviewed_by
        ");
        
    }
}
