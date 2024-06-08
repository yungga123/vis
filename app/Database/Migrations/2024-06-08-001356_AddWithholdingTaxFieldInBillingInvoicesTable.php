<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWithholdingTaxFieldInBillingInvoicesTable extends Migration
{
    private CONST TABLE = 'billing_invoices';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'withholding_tax_percent' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'amount_paid',
            ],
            'withholding_tax' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
                'after' => 'amount_paid',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['withholding_tax_percent', 'withholding_tax']);
    }
}