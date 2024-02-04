<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRemarksFieldInPRFItemsTable extends Migration
{
    private CONST TABLE = 'prf_items';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'remarks' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, 'remarks');
    }
}
