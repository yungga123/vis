<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PayrollDeductionsTable extends Migration
{
    private CONST TABLE = 'payroll_deductions';

    public function up()
    {
        $this->forge->addField([
            'payroll_id' => [
                'type' => 'INT',
                'comment' => 'Foreign key/id of payroll table',
            ],
            'days_absent' => [
                'type' => 'DECIMAL',
                'constraint' => [2,1],
            ],
            'days_absent_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'hours_late' => [
                'type' => 'DECIMAL',
                'constraint' => [2,1],
            ],
            'hours_late_amt' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'addt_rest_days' => [
                'type' => 'DECIMAL',
                'constraint' => [2,1],
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

        $this->forge->addForeignKey('payroll_id', 'payroll', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}
