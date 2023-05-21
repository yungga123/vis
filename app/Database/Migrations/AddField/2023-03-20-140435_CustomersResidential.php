<?php

namespace App\Database\Migrations\AddField;

use CodeIgniter\Database\Migration;

class CustomersResidential extends Migration
{
    private $_table = 'customers_residential';
    private $_dropField = [
        'referred_by'
    ];
    private $_addField = [
        'referred_by' => [
            'type' => 'VARCHAR',
            'constraint' => 250,
            'after' => 'notes'
        ]
    ];

    public function up()
    {
        $this->forge->addColumn($this->_table,$this->_addField);
    }

    public function down()
    {
        $this->forge->dropColumn($this->_table,$this->_dropField);
    }
}
