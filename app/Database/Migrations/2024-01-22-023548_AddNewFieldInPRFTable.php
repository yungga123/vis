<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewFieldInPRFTable extends Migration
{
    private CONST TABLE = 'project_request_forms';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'received_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'item_out_by',
                'comment' => 'Use username',
            ],
            'received_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
                'after' => 'item_out_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['received_by', 'received_at']);
    }
}
