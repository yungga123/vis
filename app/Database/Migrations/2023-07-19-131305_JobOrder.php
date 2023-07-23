<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class JobOrder extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'tasklead_id' => [
                'type' => 'INT',
                'comment' => 'Connected to task_lead_booked table',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'work_type' => [
                'type' => 'VARCHAR',
                'constraint' => 150
            ],
            'comments' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'date_requested' => [
                'type' => 'DATE',
            ],
            'date_reported' => [
                'type' => 'DATE',
            ],
            'date_committed' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'warranty' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Requested by. Use username',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default null on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('job_orders');
    }

    public function down()
    {
        $this->forge->dropTable('job_orders');
    }
}
