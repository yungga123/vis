<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdditionalDescriptionFieldInBillingInvoicesTableForTasklead extends Migration
{
    private CONST TABLE = 'billing_invoices';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'additional_description' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'grand_total',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['additional_description']);
    }
}
