<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyFieldInPayrollTable extends Migration
{
    private CONST TABLE = 'payroll';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'working_days' => [
                'type' => 'FLOAT',
                'default' => 0,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'working_days' => [
                'type' => 'DECIMAL[2,2]',
            ],
        ]);
    }
}
