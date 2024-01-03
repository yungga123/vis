<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyFieldsDecimalInPayrollEarningsTable extends Migration
{
    private CONST TABLE = 'payroll_earnings';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'working_days_off_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'over_time_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'night_diff_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'regular_holiday_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'special_holiday_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'vacation_leave_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'sick_leave_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'incentives' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'commission' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'thirteenth_month' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'add_back' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'working_days_off_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'over_time_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'night_diff_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'regular_holiday_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'special_holiday_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'vacation_leave_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'sick_leave_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'incentives' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'commission' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'thirteenth_month' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'add_back' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
        ]);
    }
}
