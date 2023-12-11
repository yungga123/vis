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
            CONCAT(tasklead.status,'%') as status,
            tasklead_status.status_percent,
            tasklead.customer_type,
            tasklead.existing_customer,
            tasklead.customer_id,
            customers.name as customer_name,
            customer_branches.branch_name,
            customers.contact_number as contact_number,
            tasklead.project,
            tasklead.project_amount,
            tasklead.quotation_num,
            tasklead.tasklead_type,
            DATE_FORMAT(forecast_close_date,'%b %d, %Y') as forecast_close_date,
            DATE_FORMAT(DATE_SUB(tasklead.forecast_close_date, INTERVAL 6 DAY),'%b %d, %Y') as min_forecast_date,
            DATE_FORMAT(DATE_ADD(tasklead.forecast_close_date, INTERVAL 6 DAY),'%b %d, %Y') as max_forecast_date,
            IF(tasklead.close_deal_date<DATE_ADD(tasklead.forecast_close_date, INTERVAL 6 DAY) AND tasklead.close_deal_date>DATE_SUB(tasklead.forecast_close_date, INTERVAL 6 DAY),'HIT','MISSED') as status1,
            tasklead.remark_next_step,
            DATE_FORMAT(tasklead.close_deal_date,'%b %d, %Y') as close_deal_date,
            DATE_FORMAT(tasklead.project_start_date,'%b %d, %Y') as project_start_date,
            DATE_FORMAT(tasklead.project_finish_date,'%b %d, %Y') as project_finish_date,
            CONCAT(DATEDIFF(tasklead.project_finish_date,project_start_date),' day/s') as project_duration,
            tasklead.created_at,
            tasklead.updated_at,
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
        LEFT JOIN
            customer_branches
        ON
            tasklead.branch_id=customer_branches.id
        WHERE
            status = 100.00
        ");
    }
}
