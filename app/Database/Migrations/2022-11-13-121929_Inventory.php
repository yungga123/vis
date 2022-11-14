<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Inventory extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'item_name' => [
                'type' => "VARCHAR",
                'constraint' => 200
            ],
            'item_brand' => [
                'type' => "VARCHAR",
                'constraint' => 200
            ],
            'item_type' => [
                'type' => "VARCHAR",
                'constraint' => 500
            ],
            'item_sdp' => [
                'type' => "DECIMAL",
                'constraint' => [18,2]
            ],
            'item_srp' => [
                'type' => "DECIMAL",
                'constraint' => [18,2]
            ],
            'project_price' => [
                'type' => "DECIMAL",
                'constraint' => [18,2]
            ],
            'stocks' => [
                'type' => "DECIMAL",
                'constraint' => [18,2]
            ],
            'stock_unit' => [
                'type' => "VARCHAR",
                'constraint' => 100
            ],
            'date_of_purchase' => [
                'type' => "DATE"
            ],
            'location' => [
                'type' => "VARCHAR",
                'constraint' => 200
            ],
            'supplier' => [
                'type' => "VARCHAR",
                'constraint' => 200
            ],
            'encoder' => [
                'type' => "VARCHAR",
                'constraint' => 200
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at datetime default null'
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('inventory');
    }

    public function down()
    {
        $this->forge->dropTable('inventory');
    }
}
