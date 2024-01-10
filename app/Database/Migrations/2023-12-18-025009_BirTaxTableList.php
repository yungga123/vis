<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BirTaxTableList extends Migration
{
    private CONST TABLE = 'bir_taxes';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'compensation_range_start' => [
                'type' => 'FLOAT',
            ],
            'compensation_range_end' => [
                'type' => 'FLOAT',
            ],
            'fixed_tax_amount' => [
                'type' => 'FLOAT',
            ],
            'compensation_level' => [
                'type' => 'FLOAT',
            ],
            'tax_rate' => [
                'type' => 'FLOAT',
            ],
            'below_or_above' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default null on update current_timestamp',
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}
