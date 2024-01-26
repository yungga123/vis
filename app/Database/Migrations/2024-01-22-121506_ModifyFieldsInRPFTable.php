<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyFieldsInRPFTable extends Migration
{
    private CONST TABLE = 'request_purchase_forms';

    public function up()
    {
        $this->forge->dropColumn(self::TABLE, ['received_by', 'received_at']);
    }

    public function down()
    {
        $this->forge->addColumn(self::TABLE, [
            'received_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
                'null' => true
            ],
            'received_at datetime default null',
        ]);
    }
}
