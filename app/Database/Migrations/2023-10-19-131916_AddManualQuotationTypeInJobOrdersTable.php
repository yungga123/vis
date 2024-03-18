<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddManualQuotationTypeInJobOrdersTable extends Migration
{
    private CONST TABLE = 'job_orders';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'manual_quotation_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'manual_quotation',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['manual_quotation_type']);
    }
}
