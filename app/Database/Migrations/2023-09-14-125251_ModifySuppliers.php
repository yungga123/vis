<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifySuppliers extends Migration
{
    private CONST TABLE = 'suppliers';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'email_address' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'product',
            ],
            'bank_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'email_address',
            ],
            'bank_number' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'bank_name',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, 'email_address');
        $this->forge->dropColumn(self::TABLE, 'bank_name');
        $this->forge->dropColumn(self::TABLE, 'bank_number');
    }
}
