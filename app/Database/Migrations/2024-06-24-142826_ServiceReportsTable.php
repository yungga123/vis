<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ServiceReportsTable extends Migration
{
    private CONST TABLE = 'service_reports';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pending',
            ],
            'job_order_id' => [
                'type' => 'INT',
                'comment' => 'Connected to job_orders table',
            ],
            'serial_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'server_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'service_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'arrival_at' => [
                'type' => 'DATETIME',
            ],
            'error_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'corrective_action' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'labor' => [
                'type' => 'FLOAT',
                'null' => true,
                'comment' => 'Hours',
            ],
            'travel_time' => [
                'type' => 'FLOAT',
                'null' => true,
                'comment' => 'Hours',
            ],
            'time_out' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
            ],
            'accepted_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username - connected to accounts table',
                'null' => true
            ],
            'filed_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username - connected to accounts table',
                'null' => true
            ],
            'created_at datetime default current_timestamp',
            'accepted_at datetime default null',
            'filed_at datetime default null',
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
