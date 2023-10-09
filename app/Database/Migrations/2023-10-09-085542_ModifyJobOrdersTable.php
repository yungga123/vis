<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyJobOrdersTable extends Migration
{
    private CONST TABLE = 'job_orders';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'customer_branch_id' => [
                'type' => 'INT',
                'after' => 'customer_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['is_manual', 'manual_quotation']);
    }
}
