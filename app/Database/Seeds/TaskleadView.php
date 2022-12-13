<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TaskleadView extends Seeder
{
    public function run()
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
            DATE_FORMAT(forecast_close_date,'%b %d, %Y') as forecast_close_date,
            IF(status>90,'YES','NO') as status1,
            remark_next_step,
            DATE_FORMAT(close_deal_date,'%b %d, %Y') as close_deal_date,
            DATE_FORMAT(project_start_date,'%b %d, %Y') as project_start_date,
            DATE_FORMAT(project_finish_date,'%b %d, %Y') as project_finish_date,
            CONCAT(DATEDIFF(project_finish_date,project_start_date),' day/s') as project_duration
        FROM 
            tasklead
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
}
