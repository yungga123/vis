<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldToJobOrdersTable extends Migration
{
    private const TABLE = 'job_orders';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'employee_id' => [
                'type'          => 'VARCHAR',
                'constraint'    => '100',
                'null'          => true,
                'after'         => 'tasklead_id',
                'comment'       => 'Person incharge',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, 'employee_id');
    }
}
