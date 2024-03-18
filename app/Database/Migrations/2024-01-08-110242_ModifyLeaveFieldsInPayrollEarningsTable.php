<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyLeaveFieldsInPayrollEarningsTable extends Migration
{
    
    private CONST TABLE = 'payroll_earnings';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'vacation_leave' => [
                'name' => 'service_incentive_leave',
                'type' => 'FLOAT',
                'default' => 0,
            ],
            'vacation_leave_amt' => [
                'name' => 'service_incentive_leave_amt',
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
        ]);

        $this->forge->dropColumn(self::TABLE, ['sick_leave', 'sick_leave_amt']);
    }

    public function down()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'service_incentive_leave' => [
                'name' => 'vacation_leave',
            ],
            'service_incentive_leave_amt' => [
                'name' => 'vacation_leave_amt',
            ],
        ]);

        $this->forge->addColumn(self::TABLE, [
            'sick_leave' => [
                'type' => 'FLOAT',
                'default' => 0,
                'after' => 'vacation_leave_amt',
            ],
            'sick_leave_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
                'after' => 'sick_leave',
            ],
        ]);
    }
}
