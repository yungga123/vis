<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewFieldInOrderFormsTable extends Migration
{
    private CONST TABLE = 'order_forms';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'received_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username - connected to accounts table',
                'null' => true,
                'after' => 'item_out_by',
            ],
            'received_at datetime default null after item_out_at',
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['received_by', 'received_at']);
    }
}
