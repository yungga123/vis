<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddModuleFieldInFundsHistoryTable extends Migration
{
    private CONST TABLE = 'funds_history';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'module_code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'coming_from',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['module_code']);
    }
}
