<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveCustomerFieldsInDispatchTable extends Migration
{
    private CONST TABLE = 'dispatch';

    public function up()
    {
        $this->forge->dropColumn(self::TABLE, ['customer_id', 'customer_type']);
    }

    public function down()
    {
        $this->forge->addColumn(self::TABLE, [
            'customer_id' => [
                'type' => 'INT',
                'comment' => 'Connected to customers (residential or commercial)',
            ],
            'customer_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'commercial',
            ],
        ]);
    }
}
