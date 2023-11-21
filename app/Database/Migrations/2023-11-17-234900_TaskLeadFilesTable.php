<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskLeadFilesTable extends Migration
{
    private CONST TABLE = 'tasklead_files';

    public function up()
    {
        $this->forge->addField([
            'tasklead_id' => [
                'type' => 'INT',
                'comment' => 'Connected to tasklead or task_lead_booked table using primary key'
            ],
            'filenames' => [
                'type' => 'TEXT',
                'comment' => 'Json format'
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addForeignKey('tasklead_id', 'tasklead', 'id', '', 'CASCADE');
        $this->forge->addKey('tasklead_id', false, true, 'tasklead_id_unique_key');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}
