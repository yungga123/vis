<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewFieldsInBillingInvoicesTable extends Migration
{
    private CONST TABLE = 'billing_invoices';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'overdue_interest' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
                'after' => 'with_vat',
            ],
            'paid_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
                'null' => true,
                'after' => 'amount_paid',
            ],
            'vat_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
                'after' => 'with_vat',
            ],
            'grand_total' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
                'after' => 'overdue_interest',
                'comment' => 'billing_amount + vat_amount | not include overdue_interest',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['overdue_interest', 'paid_by', 'vat_amount']);
    }
}
