<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateInventoryTableFields extends Migration
{
    private CONST TABLE = 'inventory';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'encoder' => [
                'name' => 'created_by',
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
            ],
            'stocks' => [
                'type' => 'DECIMAL',
                'constraint' => [7,2]
            ],
            'date_of_purchase' => [
                'type' => 'DATE',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn(self::TABLE, [            
            'created_by' => [
                'name' => 'encoder',
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Using employee id',
            ],
            'stocks' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2]
            ],
            'date_of_purchase' => [
                'type' => 'DATE',
            ],
        ]);
    }
}
