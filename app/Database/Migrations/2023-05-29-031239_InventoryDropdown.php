<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InventoryDropdown extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'dropdown_id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'dropdown' => [
                'type' => "VARCHAR",
                'constraint' => 200
            ],
            'dropdown_type' => [
                'type' => "VARCHAR",
                'constraint' => 50
            ],
            'other_category_type' => [
                'type' => "VARCHAR",
                'constraint' => 50
            ],
            'parent_id' => [
                'type' => "INT",
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
        $this->forge->addPrimaryKey('dropdown_id');
        $this->forge->createTable('inventory_dropdowns');
    }

    public function down()
    {
        $this->forge->dropTable('inventory_dropdowns');
    }
}
