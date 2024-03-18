<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyInventoryField extends Migration
{
    private CONST TABLE = 'inventory';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'supplier' => [
                'type' => 'INT',
                'name' => 'supplier_id',
                'comment' => 'Connected to suppliers table',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn(self::TABLE, [            
            'supplier' => [
                'type' => 'VARCHAR',
                'constraint' => 200
            ],
        ]);
    }
}
