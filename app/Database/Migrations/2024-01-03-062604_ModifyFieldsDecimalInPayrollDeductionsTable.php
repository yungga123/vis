<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyFieldsDecimalInPayrollDeductionsTable extends Migration
{
    private CONST TABLE = 'payroll_deductions';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'days_absent_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'hours_late_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'addt_rest_days_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'govt_sss' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'govt_pagibig' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'govt_philhealth' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'withholding_tax' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'cash_advance' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'others' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'days_absent_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'hours_late_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'addt_rest_days_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'govt_sss' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'govt_pagibig' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'govt_philhealth' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'withholding_tax' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'cash_advance' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'others' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
        ]);
    }
}
