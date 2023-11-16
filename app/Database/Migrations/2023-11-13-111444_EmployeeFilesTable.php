<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CustomerFilesTable extends Migration
{
    private CONST TABLE = 'customer_files';

    public function up()
    {
        $this->forge->addField([
            'customer_id' => [
                'type' => 'INT',
                'comment' => 'Connected to customers table using primary key'
            ],
            'file_names' => [
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

        $this->forge->addForeignKey('customer_id', 'customers', 'id', '', 'CASCADE');
        $this->forge->addKey('customer_id', false, true, 'customer_id_unique_key');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}
