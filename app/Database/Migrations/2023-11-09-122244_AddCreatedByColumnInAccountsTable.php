<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedByColumnInAccountsTable extends Migration
{
    private CONST TABLE = 'accounts';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'profile_img',
                'comment' => 'Use employee_id - connected to employees table',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['created_by']);
    }
}
