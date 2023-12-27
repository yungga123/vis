<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyFieldsInPayrollEarningsTable extends Migration
{
    private CONST TABLE = 'payroll_earnings';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'working_days_off' => [
                'type' => 'FLOAT',
                'default' => 0,
            ],
            'over_time' => [
                'type' => 'FLOAT',
                'default' => 0,
            ],
            'night_diff' => [
                'type' => 'FLOAT',
                'default' => 0,
            ],
            'regular_holiday' => [
                'type' => 'FLOAT',
                'default' => 0,
            ],
            'special_holiday' => [
                'type' => 'FLOAT',
                'default' => 0,
            ],
            'vacation_leave' => [
                'type' => 'FLOAT',
                'default' => 0,
            ],
            'sick_leave' => [
                'type' => 'FLOAT',
                'default' => 0,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'working_days_off' => [
                'type' => 'DECIMAL[2,2]',
            ],
            'over_time' => [
                'type' => 'DECIMAL[2,2]',
            ],
            'night_diff' => [
                'type' => 'DECIMAL[2,2]',
            ],
            'regular_holiday' => [
                'type' => 'DECIMAL[2,2]',
            ],
            'special_holiday' => [
                'type' => 'DECIMAL[2,2]',
            ],
            'vacation_leave' => [
                'type' => 'DECIMAL[2,2]',
            ],
            'sick_leave' => [
                'type' => 'DECIMAL[2,2]',
            ],
        ]);
    }
}
