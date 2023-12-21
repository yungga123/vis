<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyOvertimeTableFields extends Migration
{
    private CONST TABLE = 'overtime';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'total_hours' => [
                'type' => 'TIME',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'total_hours' => [
                'type' => 'DOUBLE',
            ],
        ]);
    }
}
