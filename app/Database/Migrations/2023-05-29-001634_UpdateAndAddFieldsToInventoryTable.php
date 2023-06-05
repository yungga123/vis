<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAndAddFieldsToInventoryTable extends Migration
{
    private const TABLE = 'inventory';

    public function up()
    {
        $alter_fields = [
            'item_brand' => [
                'type' => "INT",
                'constraint' => '',
                'comment' => 'Connected to inventory_dropdowns',
            ],
            'item_type' => [
                'name' => "item_description",
                'type' => "VARCHAR",
                'constraint' => 500
            ],
            'stock_unit' => [
                'type' => "INT",
                'null' => true,
                'comment' => 'Connected to inventory_dropdowns',
            ],
            'location' => [
                'type' => "VARCHAR",
                'constraint' => 200,
                'null' => true,
            ],
            'supplier' => [
                'type' => "VARCHAR",
                'constraint' => 200,
                'null' => true,
            ],
            'encoder' => [
                'type' => "VARCHAR",
                'constraint' => 100,
                'comment' => 'Using employee id',
            ],
        ];

        $add_fields = [
            'category' => [
                'type' => "INT",
                'after' => 'id',
                'comment' => 'Connected to inventory_dropdowns type category',
            ],
            'sub_category' => [
                'type' => "INT",
                'after' => 'category',
                'comment' => 'Connected to inventory_dropdowns',
            ],
            'item_model' => [
                'type' => "VARCHAR",
                'constraint' => 150,
                'after' => 'item_brand',
            ],
            'item_size' => [
                'type' => "INT",
                'null' => true,
                'after' => 'item_description',
                'comment' => 'Connected to inventory_dropdowns',
            ],
            'total' => [
                'type' => "INT",
                'after' => 'project_price',
            ],
        ];

        $drop_fields = ['item_name'];

        $this->forge->modifyColumn(self::TABLE, $alter_fields);
        $this->forge->addColumn(self::TABLE, $add_fields);
        $this->forge->dropColumn(self::TABLE, $drop_fields);
    }

    public function down()
    {
        $alter_fields = [
            'item_brand' => [
                'type' => "VARCHAR",
                'constraint' => 200
            ],
            'item_description' => [
                'name' => "item_type",
                'type' => "VARCHAR",
                'constraint' => 500
            ],
            'stock_unit' => [
                'type' => "VARCHAR",
                'constraint' => 100,
            ],
            'location' => [
                'type' => "VARCHAR",
                'constraint' => 200,
                'null' => false,
            ],
            'supplier' => [
                'type' => "VARCHAR",
                'constraint' => 200,
                'null' => false,
            ],
        ];

        
        $add_fields = [
            'item_name' => [
                'type' => "VARCHAR",
                'constraint' => 200,
                'after' => 'id',
            ],
        ];

        $drop_fields = [
            'category',
            'sub_category',
            'item_model',
            'item_size',
            'total',
        ];
        
        $this->forge->modifyColumn(self::TABLE, $alter_fields);
        $this->forge->addColumn(self::TABLE, $add_fields);
        $this->forge->dropColumn(self::TABLE, $drop_fields);
    }
}
