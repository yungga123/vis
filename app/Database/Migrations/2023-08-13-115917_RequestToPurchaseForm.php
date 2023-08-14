<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RequestToPurchaseForm extends Migration
{
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
                'default'=> "pending",
            ],
            'date_needed' => [
                'type' => 'date',
                'null' => true
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
            'reviewed_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
                'null' => true
            ],
            'received_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
                'null' => true
            ],
            'created_at datetime default current_timestamp',
            'accepted_at datetime default null',
            'rejected_at datetime default null',
            'reviewed_at datetime default null',
            'received_at datetime default null',
            'updated_at datetime default null on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('request_purchase_forms');
    }

    public function down()
    {
        $this->forge->dropTable('request_purchase_forms');
    }
}
