<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TaskleadView extends Seeder
{
    public function run()
    {
        $this->db->query("DROP VIEW IF EXISTS task_lead");
        $this->db->query("
            CREATE VIEW 
                task_lead 
            AS SELECT 
                tasklead.id AS id,
                tasklead.employee_id,
                CONCAT(employees.firstname,' ',employees.lastname) AS employee_name,
                tasklead.quarter,
                CONCAT(tasklead.status,'%') AS status,
                tasklead_status.status_percent,
                tasklead.customer_type,
                tasklead.existing_customer,
                customers.name AS customer_name,
                customer_branches.branch_name,
                customers.contact_number,
                tasklead.project,
                FORMAT(ROUND(tasklead.project_amount, 2), 2) AS project_amount,
                tasklead.quotation_num,
                tasklead.tasklead_type,
                DATE_FORMAT(forecast_close_date,'%b %d, %Y') AS forecast_close_date,
                DATE_FORMAT(DATE_SUB(forecast_close_date, INTERVAL 6 DAY), '%b %d, %Y') AS min_forecast_date,
                DATE_FORMAT(DATE_ADD(forecast_close_date, INTERVAL 6 DAY), '%b %d, %Y') AS max_forecast_date,
                IF(close_deal_date < DATE_ADD(forecast_close_date, INTERVAL 6 DAY) AND close_deal_date > DATE_SUB(tasklead.forecast_close_date, INTERVAL 6 DAY), 'HIT', 'MISSED') AS status1,
                tasklead.remark_next_step,
                DATE_FORMAT(tasklead.close_deal_date, '%b %d, %Y') AS close_deal_date,
                DATE_FORMAT(tasklead.project_start_date, '%b %d, %Y') AS project_start_date,
                DATE_FORMAT(tasklead.project_finish_date, '%b %d, %Y') AS project_finish_date,
                CONCAT(DATEDIFF(tasklead.project_finish_date, tasklead.project_start_date),' day/s') AS project_duration,
                accounts_view.employee_name AS created_by,
                DATE_FORMAT(tasklead.created_at, '%b %e, %Y at %h:%i %p') AS created_at
            FROM 
                tasklead
            LEFT JOIN
                customers
            ON
                tasklead.customer_id = customers.id
            LEFT JOIN
                tasklead_status
            ON
                tasklead.status = tasklead_status.percent
            LEFT JOIN
                employees
            ON
                tasklead.employee_id = employees.employee_id
            LEFT JOIN
                customer_branches
            ON
                tasklead.branch_id = customer_branches.id
            LEFT JOIN
                accounts_view
            ON
                tasklead.created_by = accounts_view.username
            WHERE
                tasklead.deleted_at IS NULL
        ");
    }
}
