<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PayrollEarningsTable extends Migration
{
    private CONST TABLE = 'payroll_earnings';

    public function up()
    {
        $this->forge->addField([
            'payroll_id' => [
                'type' => 'INT',
                'comment' => 'Foreign key/id of payroll table',
            ],
            'working_days_off' => [
                'type' => 'DECIMAL',
                'constraint' => [2,1],
            ],
            'working_days_off_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'over_time' => [
                'type' => 'DECIMAL',
                'constraint' => [2,1],
            ],
            'over_time_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'night_diff' => [
                'type' => 'DECIMAL',
                'constraint' => [2,1],
            ],
            'night_diff_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'regular_holiday' => [
                'type' => 'DECIMAL',
                'constraint' => [2,1],
            ],
            'regular_holiday_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'special_holiday' => [
                'type' => 'DECIMAL',
                'constraint' => [2,1],
            ],
            'special_holiday_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'vacation_leave' => [
                'type' => 'DECIMAL',
                'constraint' => [2,1],
            ],
            'sick_leave' => [
                'type' => 'DECIMAL',
                'constraint' => [2,1],
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

        $this->forge->addForeignKey('payroll_id', 'payroll', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}
