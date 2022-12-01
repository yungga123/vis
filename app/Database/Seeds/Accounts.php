<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Accounts extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $table = 'accounts';
        $data = [
            [
                'employee_id'   => 1,
                'username'      => 'yungga',
                'password'      => 'yunggabells',
                'access_level'      => 'admin'
            ]
        ];
        $db->table($table)->insertBatch($data);
    }
}
