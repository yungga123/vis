<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSupplierField extends Migration
{
    private CONST TABLE = 'suppliers';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'bank_account_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'bank_name',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, 'bank_account_name');
    }
}
