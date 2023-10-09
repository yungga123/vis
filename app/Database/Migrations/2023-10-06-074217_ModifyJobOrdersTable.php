<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyJobOrdersTable extends Migration
{
    private CONST TABLE = 'job_orders';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'customer_id' => [
                'type' => 'INT',
                'after' => 'remarks',
            ],
            'manual_quotation' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'remarks',
            ],
            'is_manual' => [
                'type' => 'BOOLEAN',
                'default' => FALSE,
                'after' => 'remarks',
                'comment' => 'Identifier whether quotation was added manually or not',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['customer_id', 'is_manual', 'manual_quotation']);
    }
}
