<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyJobOrdersTableAgain extends Migration
{
    private CONST TABLE = 'job_orders';

    public function up()
    {
        $db     = \Config\Database::connect();

        if (! $db->fieldExists('customer_branch_id', self::TABLE)) {
            $this->forge->addColumn(self::TABLE, [
                'customer_branch_id' => [
                    'type' => 'INT',
                    'after' => 'customer_id',
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['customer_branch_id']);
    }
}
