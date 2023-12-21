<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyLeaveTableFields extends Migration
{
    private CONST TABLE = 'leave';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'total_days' => [
                'type' => 'FLOAT',
                'default' => 0,
                'after' => 'end_date',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, 'total_days');
    }
}
