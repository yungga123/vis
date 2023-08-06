<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProjectRequestFormMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'inventory_id' => [
                'type' => 'INT',
                'comment' => 'Connected to inventory table',
            ],
            'quantity_out' => [
                'type' => 'DECIMAL',
                'constraint' => [7,2]
            ],
            'process_date' => [
                'type' => 'date',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default'=> "pending",
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
                'null' => true
            ],
            'accepted_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
                'null' => true
            ],
            'rejected_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
                'null' => true
            ],
            'item_out_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
                'null' => true
            ],
            'created_at datetime default current_timestamp',
            'accepted_at datetime default null',
            'rejected_at datetime default null',
            'item_out_at datetime default null',
            'updated_at datetime default null on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('project_request_forms');
    }

    public function down()
    {
        $this->forge->dropTable('project_request_forms');
    }
}
