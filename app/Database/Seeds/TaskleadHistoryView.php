<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TaskleadHistoryView extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query("
            DROP VIEW IF EXISTS
                task_lead_history_view
        ");
        $db->query("
        CREATE VIEW 
            task_lead_history_view 
        AS SELECT
            id,
            tasklead_id,
            quarter,
            CONCAT(status,'%') as status,
            CASE
                WHEN status = 10.00 THEN 'IDENTIFIED'
                WHEN status = 30.00 THEN 'QUALIFED'
                WHEN status = 50.00 THEN 'DEVELOPED SOLUTION'
                WHEN status = 70.00 THEN 'EVALUATION'
                WHEN status = 90.00 THEN 'NEGOTIATION'
                WHEN status = 100.00 THEN 'BOOKED'
            END AS status_percent,
            project,
            project_amount,
            quotation_num,
            DATE_FORMAT(forecast_close_date,'%b %d, %Y') as forecast_close_date,
            DATE_FORMAT(DATE_SUB(forecast_close_date, INTERVAL 6 DAY),'%b %d, %Y') as min_forecast_date,
            DATE_FORMAT(DATE_ADD(forecast_close_date, INTERVAL 6 DAY),'%b %d, %Y') as max_forecast_date,
            IF(close_deal_date<DATE_ADD(forecast_close_date, INTERVAL 6 DAY) AND close_deal_date>DATE_SUB(forecast_close_date, INTERVAL 6 DAY),'HIT','MISSED') as status1,
            remark_next_step,
            DATE_FORMAT(close_deal_date,'%b %d, %Y') as close_deal_date,
            DATE_FORMAT(project_start_date,'%b %d, %Y') as project_start_date,
            DATE_FORMAT(project_finish_date,'%b %d, %Y') as project_finish_date,
            CONCAT(DATEDIFF(project_finish_date,project_start_date),' day/s') as project_duration,
            tasklead_history.deleted_at,
            DATE_FORMAT(tasklead_history.created_at,'%r') as created_at
            
        FROM 
            tasklead_history
        WHERE
            tasklead_history.deleted_at IS NULL
        ");
    }
}
