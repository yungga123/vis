<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ChangeRPFStatus extends Seeder
{
    public function run()
    {
        // Change RPF with status of RECEIVED to REVIEWED
        $table = 'request_purchase_forms';
        $this->db->query("UPDATE `{$table}` SET `status` = 'reviewed' WHERE `status` = 'received'");
    }
}
