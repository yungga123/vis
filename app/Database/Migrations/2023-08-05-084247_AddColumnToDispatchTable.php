<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnToDispatchTable extends Migration
{
    private CONST TABLE = 'dispatch';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'checked_by' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'created_by',
                'comment' => 'Use employee_id',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, 'checked_by');
    }
}
