<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApprovedByNAtInBillingInvoicesTable extends Migration
{
    private CONST TABLE = 'billing_invoices';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pending',
                'after' => 'id',
            ],
            'approved_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
                'null' => true,
                'after' => 'created_by',
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['status', 'approved_by', 'approved_at']);
    }
}