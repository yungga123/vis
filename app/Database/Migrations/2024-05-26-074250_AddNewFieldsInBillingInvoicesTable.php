<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewFieldsInBillingInvoicesTable extends Migration
{
    private CONST TABLE = 'billing_invoices';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'date_paid date default null after amount_paid',
            'receipt_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'paid_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['date_paid', 'receipt_number']);
    }
}