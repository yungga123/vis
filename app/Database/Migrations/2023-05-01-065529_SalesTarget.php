<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SalesTarget extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'sales_id' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'q1_target' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true
            ],
            'q2_target' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true
            ],
            'q3_target' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true
            ],
            'q4_target' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true
            ],
            'created_at' => [
                'type' => "datetime"
            ],
            'updated_at' => [
                'type' => "datetime",
                'null' => true
            ],
            'deleted_at' => [
                'type' => "datetime",
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('sales_target');
    }

    public function down()
    {
        $this->forge->dropTable('sales_target');
    }
}
