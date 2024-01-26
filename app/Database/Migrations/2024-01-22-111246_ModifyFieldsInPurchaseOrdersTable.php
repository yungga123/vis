<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyFieldsInPurchaseOrdersTable extends Migration
{
    private CONST TABLE = 'purchase_orders';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'filed_by' => [
                'name' => 'received_by',
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Use username',
            ],
            'filed_at' => [
                'name' => 'received_at',
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'received_by' => [
                'name' => 'filed_by',
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Use username',
            ],
            'received_at' => [
                'name' => 'filed_at',
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }
}
