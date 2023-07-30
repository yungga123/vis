<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Dispatch extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'schedule_id' => [
                'type' => 'INT',
                'comment' => 'Connected to schedules table',
            ],
            'customer_id' => [
                'type' => 'INT',
                'comment' => 'Connected to customers (residential or commercial)',
            ],
            'customer_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'commercial',
            ],
            'sr_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'dispatch_out' => [
                'type' => 'time',
                'default' => '00:00:00',
            ],
            'dispatch_date' => [
                'type' => 'date',
            ],
            'time_in' => [
                'type' => 'time',
                'default' => '00:00:00',
            ],
            'time_out' => [
                'type' => 'time',
                'default' => '00:00:00',
            ],
            'service_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'comments' => [
                'type' => 'TEXT',
            ],
            'with_permit' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default null on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('dispatch');
    }

    public function down()
    {
        $this->forge->dropTable('dispatch');
    }
}
