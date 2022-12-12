<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TaskleadStatus extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $table = 'tasklead_status';
        $data = [
            [
                'percent'           => 10,
                'status_percent'    => 'Identified'
            ],
            [  
                'percent'           => 30,
                'status_percent'    => 'Negotiation'
            ],
            [
                'percent'           => 50,
                'status_percent'    => 'Evaluation'
            ],
            [
                'percent'           => 70,
                'status_percent'    => 'Developed Solution'
            ],
            [
                'percent'           => 90,
                'status_percent'    => 'Qualified'
            ],
            [
                'percent'           => 100,
                'status_percent'    => 'Booked'
            ]
        ];
        $db->table($table)->insertBatch($data);
    }
}
