<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JobOrdersView extends Seeder
{
    public function run()
    {
        $table  = 'job_orders';
        $view   = 'job_orders_view';

        // Drop if exists
        $this->db->query("DROP VIEW IF EXISTS {$view}");
        $this->db->query("DROP TABLE IF EXISTS {$view}");

        // Create
        $this->db->query("
            CREATE VIEW {$view}
            AS 
            SELECT
                {$table}.id AS job_order_id,
                IF({$table}.is_manual = 0, tl.customer_id, {$table}.customer_id) AS client_id,
                IF({$table}.is_manual = 0, ctl.name, cjo.name) AS client_name,
                IF({$table}.is_manual = 0, ctl.type, cjo.type) AS client_type,
                IF({$table}.is_manual = 0, tl.branch_id, {$table}.customer_branch_id) AS client_branch_id,
                cbjo.branch_name AS client_branch_name,
                IF({$table}.is_manual = 0, tl.quotation_num, {$table}.manual_quotation) AS quotation,
                IF({$table}.is_manual = 0, tl.tasklead_type, {$table}.manual_quotation_type) AS quotation_type,
                CONCAT(emp.firstname, ' ', emp.lastname) AS manager,
                {$table}.deleted_at
            FROM {$table}
            LEFT JOIN task_lead_booked AS tl
                ON tl.id = {$table}.tasklead_id
            LEFT JOIN customers cjo
                ON cjo.id = {$table}.customer_id
            LEFT JOIN customers ctl
                ON ctl.id = tl.customer_id
            LEFT JOIN customer_branches cbjo
                ON cbjo.id = IF({$table}.is_manual = 0, tl.branch_id, {$table}.customer_branch_id)
            LEFT JOIN employees emp
                ON emp.employee_id = {$table}.employee_id
            WHERE 
                {$table}.`deleted_at` IS NULL
        ");
    }
}
