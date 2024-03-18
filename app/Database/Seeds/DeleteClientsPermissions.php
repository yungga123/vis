<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DeleteClientsPermissions extends Seeder
{
    public function run()
    {
        $db         = \Config\Database::connect();
        $table      = 'permissions';
        $condition  = ['CUSTOMERS_COMMERCIAL', 'CUSTOMERS_RESIDENTIAL'];

        $db->table($table)->whereIn('module_code', $condition)->delete();
    }
}
