<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAddressFieldInSuppliersTable extends Migration
{
    private CONST TABLE = 'suppliers';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'others_supplier_type',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['address']);
    }
}
