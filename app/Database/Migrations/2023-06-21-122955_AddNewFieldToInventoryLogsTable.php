<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewFieldToInventoryLogsTable extends Migration
{
    private CONST TABLE = 'inventory_logs';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'supplier',
            ],
            'status_date' => [
                'type' => 'date',
                'null' => true,
                'after' => 'status',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, 'status');
    }
}
