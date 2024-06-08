<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PRFItemsFiledUpdate extends Seeder
{
    public function run()
    {
        $prfTable       = 'project_request_forms';
        $prfItemsTable  = 'prf_items';

        // Set the new field 'is_returned' to 1 (Yes)
        // for all the FILED PRF
        $this->db->query("
            UPDATE 
                `{$prfItemsTable}`
            LEFT JOIN 
                `{$prfTable}`
                ON `{$prfItemsTable}`.`prf_id` = `{$prfTable}`.`id`
            SET 
                `{$prfItemsTable}`.`is_filed` = 1 
            WHERE 
                `{$prfTable}`.`status` = 'filed' 
                AND (`{$prfItemsTable}`.`returned_q` IS NOT NULL OR `{$prfItemsTable}`.`returned_q` > 0)
                AND `{$prfItemsTable}`.`returned_date` IS NOT NULL
        ");
    }
}