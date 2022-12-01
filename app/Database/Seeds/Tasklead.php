<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Tasklead extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $table = 'tasklead';
        $data = [
            [
                'quarter'   => 1,
                'status'   => 30,
                'customer_id'   => 1,
                'project'   => "CCTV Installation",
                'project_amount'   => "50000",
                'quotation_num'   => "QTN3487392",
                'forecast_close_date'   => "2022-05-04",
                'remark_next_step'   => "Gagstiiii",
                'close_deal_date'   => "2022-05-11",
                'project_start_date'   => "2022-06-23",
                'project_finish_date'   => "2022-06-30"
            ]
        ];
        $db->table($table)->insertBatch($data);
    }
}
