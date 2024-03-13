<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCategoryFieldInSalaryRatesTable extends Migration
{
    private CONST TABLE = 'salary_rates';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'payout' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'salary_rate',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['payout']);
    }
}