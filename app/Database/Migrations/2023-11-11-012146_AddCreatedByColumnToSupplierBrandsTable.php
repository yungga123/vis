<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedByColumnToSupplierBrandsTable extends Migration
{
    private CONST TABLE = 'supplier_brands';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'remarks',
                'comment' => 'Use username - connected to accounts table',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['created_by']);
    }
}
