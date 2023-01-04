<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TaskleadBookedView extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query("
            DROP VIEW IF EXISTS
                task_lead_booked
        ");
        $db->query("
        CREATE VIEW 
            task_lead_booked
        AS SELECT 
            tasklead.id as id,
            tasklead.employee_id,
            CONCAT(employees.firstname,' ',employees.lastname) as employee_name,
            quarter,
            CONCAT(status,'%') as status,
            status_percent,
            customer_name,
            customers.contact_number as contact_number,
            project,
            project_amount,
            quotation_num,
            DATE_FORMAT(forecast_close_date,'%b %d, %Y') as forecast_close_date,
            DATE_FORMAT(min_forecast_date,'%b %d, %Y') as min_forecast_date,
            DATE_FORMAT(max_forecast_date,'%b %d, %Y') as max_forecast_date,
            IF(close_deal_date<max_forecast_date AND close_deal_date>min_forecast_date,'HIT','MISSED') as status1,
            remark_next_step,
            DATE_FORMAT(close_deal_date,'%b %d, %Y') as close_deal_date,
            DATE_FORMAT(project_start_date,'%b %d, %Y') as project_start_date,
            DATE_FORMAT(project_finish_date,'%b %d, %Y') as project_finish_date,
            CONCAT(DATEDIFF(project_finish_date,project_start_date),' day/s') as project_duration,
            tasklead.deleted_at
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
        LEFT JOIN
            employees
        ON
            tasklead.employee_id=employees.employee_id
        WHERE
            tasklead.deleted_at IS NULL AND status = 100.00
        ");
    }
}
