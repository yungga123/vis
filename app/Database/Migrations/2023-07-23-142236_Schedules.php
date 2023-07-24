<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Schedules extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'job_order_id' => [
                'type' => 'INT',
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 200
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'start' => [
                'type' => 'datetime',
            ],
            'end' => [
                'type' => 'datetime',
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
        $this->forge->createTable('schedules');
    }

    public function down()
    {
        $this->forge->dropTable('schedules');
    }
}
