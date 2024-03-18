<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsInPayrollEarningsTable extends Migration
{
    private CONST TABLE = 'payroll_earnings';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'vacation_leave_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
                'default' => 0,
                'after' => 'vacation_leave',
            ],
            'sick_leave_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
                'default' => 0,
                'after' => 'sick_leave',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['vacation_leave_amt', 'sick_leave_amt']);
    }
}
