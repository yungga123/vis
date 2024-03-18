<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CustomerSupportSpecialistsTable extends Migration
{
    private CONST TABLE = 'customer_support_specialists';

    public function up()
    {
        $this->forge->addField([
            'customer_support_id' => [
                'type' => 'INT',
                'comment' => 'Connected to customer_supports table',
            ],
            'employee_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Connected to employees table',
            ],
        ]);

        $this->forge->addForeignKey('customer_support_id', 'customer_supports', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}