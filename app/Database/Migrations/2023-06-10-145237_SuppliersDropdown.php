<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SuppliersDropdown extends Migration
{
    public function up()
    {

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'dropdown' => [
                'type' => 'varchar',
                'constraint' => 200
            ],
            'dropdown_type' => [
                'type' => 'varchar',
                'constraint' => 50
            ],
            'parent_id' => [
                'type' => 'int',
                'default' => 0
            ],
            'created_by' => [
                'type' => "VARCHAR",
                'constraint' => 100
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('suppliers_dropdown');
    }

    public function down()
    {
        $this->forge->dropTable('suppliers_dropdown');
        
    }
}
