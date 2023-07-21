<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldToTaskleadTable extends Migration
{
    private CONST TABLE = 'tasklead';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'tasklead_type' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'project_finish_date',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, 'status');
    }
}
