<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskleadAddField extends Migration
{
    public function up()
    {
        $table = 'tasklead';
        $fields = [
            'min_forecast_date' => [
                'type' => 'DATE',
                'after' => 'forecast_close_date'
            ],
            'max_forecast_date' => [
                'type' => 'DATE',
                'after' => 'min_forecast_date'
            ]
        ];
        $this->forge->addColumn($table, $fields);
    }

    public function down()
    {
        $table = 'tasklead';
        $fields = ['min_forecast_date','max_forecast_date'];

        $this->forge->dropColumn($table,$fields);
    }
}
