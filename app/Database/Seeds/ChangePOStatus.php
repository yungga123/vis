<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ChangePOStatus extends Seeder
{
    public function run()
    {
        // Change PO with status of FILED to RECEIVED
        $table = 'purchase_orders';
        $this->db->query("UPDATE `{$table}` SET `status` = 'received' WHERE `status` = 'filed'");
    }
}
