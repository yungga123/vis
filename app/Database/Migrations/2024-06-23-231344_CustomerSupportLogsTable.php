<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CustomerSupportLogsTable extends Migration
{
    private CONST TABLE = 'customer_support_logs';

    public function up()
    {
        $this->forge->addField([
            'customer_support_id' => [
                'type' => 'INT',
                'comment' => 'Connected to customer_supports table',
            ],
            'findings' => [
                'type' => 'TEXT',
            ],
            'action' => [
                'type' => 'TEXT',
                'comment' => 'Initial action taken by the customer',
                'null' => true,
            ],
            'troubleshooting' => [
                'type' => 'TEXT',
                'comment' => 'Initial troubleshooting done?',
                'null' => true,
            ],
            'logged_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
            ],
            'logged_at datetime default current_timestamp',
        ]);

        $this->forge->addForeignKey('customer_support_id', 'customer_supports', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}
