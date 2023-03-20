<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImageColumnInAccounts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('accounts', [
            'profile_img' => [
                'type'          => 'varchar',
                'constraint'    => 255,
                'null'          => true,
                'after'         => 'access_level',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('accounts', ['profile_img']);
    }
}
