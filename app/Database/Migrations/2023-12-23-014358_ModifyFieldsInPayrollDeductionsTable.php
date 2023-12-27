<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyFieldsInPayrollDeductionsTable extends Migration
{
    private CONST TABLE = 'payroll_deductions';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'days_absent' => [
                'type' => 'FLOAT',
                'default' => 0,
            ],
            'hours_late' => [
                'type' => 'FLOAT',
                'default' => 0,
            ],
            'addt_rest_days' => [
                'type' => 'FLOAT',
                'default' => 0,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'days_absent' => [
                'type' => 'DECIMAL[2,2]',
            ],
            'hours_late' => [
                'type' => 'DECIMAL[2,2]',
            ],
            'addt_rest_days' => [
                'type' => 'FLOAT',
                'default' => 0,
            ],
        ]);
    }
}
