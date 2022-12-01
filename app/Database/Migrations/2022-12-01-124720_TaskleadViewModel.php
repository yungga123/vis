<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskleadViewModel extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $db->query("
        CREATE VIEW 
        task_lead AS SELECT 
            tasklead.id as id,
            quarter,
            CONCAT(status,'%') as status,
            status_percent,
            customer_name,
            contact_number,
            project,
            project_amount,
            quotation_num,
            forecast_close_date,
            IF(status>90,'YES','NO') as status1,
            remark_next_step,
            close_deal_date,
            project_start_date,
            project_finish_date,
            CONCAT(DATEDIFF(project_finish_date,project_start_date),' day/s') as project_duration
        FROM tasklead
        LEFT JOIN
        customers
        ON
        tasklead.customer_id=customers.id
        LEFT JOIN
        tasklead_status
        ON
        tasklead.status=tasklead_status.percent
        ");

    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query("DROP VIEW task_lead");
    }
}
