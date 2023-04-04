<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DeleteLanguageInEmployees extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('employees','language');
    }

    public function down()
    {
        $fields = [
            'language' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'after' => 'postal_code'
            ],
        ];
        $this->forge->addColumn('employees', $fields);
    }
}
