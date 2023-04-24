<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Roles extends Seeder
{
    public function run()
    {
        $db         = \Config\Database::connect();
        $table      = 'roles';
        $mainRoles  = ROLES;
        $data       = [];

        if (! empty($mainRoles)) {
            foreach ($mainRoles as $key => $val) {
                $isRoleCodeExist = $db->table($table)->where('role_code', $key)->get();
                
                if (empty($isRoleCodeExist->getRowArray())) {
                    $data[] = [
                        'role_code'     => $key,
                        'description'   => $val,
                        'created_by'    => 'yungga',
                    ];
                }                
            }
        }

        if (! empty($data)) $db->table($table)->insertBatch($data);
    }
}
