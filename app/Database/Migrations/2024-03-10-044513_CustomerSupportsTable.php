<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CustomerSupportsTable extends Migration
{
    private CONST TABLE = 'customer_supports';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'customer_id' => [
                'type' => 'INT',
                'comment' => 'Connected to customers table',
            ],
            'customer_branch_id' => [
                'type' => 'INT',
                'comment' => 'Connected to customer_branches table',
                'null' => true,
            ],
            'ticket_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'issue' => [
                'type' => 'TEXT',
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
            'security_ict_system' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'security_ict_system_other' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'priority' => [
                'type' => 'TINYINT',
            ],
            'due_date' => [
                'type' => 'DATE',
            ],
            'follow_up_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'remarks' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pending',
                'comment' => 'Final remarks',
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
            ],
            'done_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
            ],
            'turn_over_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
            ],
            'created_at datetime default current_timestamp',
            'done_at datetime default null',
            'turn_over_at datetime default null',
            'updated_at datetime default null on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}
