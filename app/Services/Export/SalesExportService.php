<?php

namespace App\Services\Export;

use App\Models\CustomerSupportModel;
use App\Models\TaskLeadModel;

class SalesExportService extends ExportService
{
    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function taskleads($filters = [], $booked = false)
    {
        $model      = new TaskLeadModel();
        $builder    = $model->dtGetTaskLeads($booked);
        $optionFN   = $booked ? 'quarter' : 'status';
        $dateFN     = $booked ? 'updated_at' : 'created_at';
        
        if (! $booked) $builder->where('status !=', $model->booked);

        // Process and add filters
        $this->processFilters($model->view, $builder, $filters, $optionFN, $dateFN);

        $query      = $builder->orderBy('id', 'ASC')->get();
        $data       = $query->getResultArray();
        $header     = [
            'Tasklead ID',
            'Employee Name',
            'Quarter',
            'Percent',
            'Status',
            'Client Name',
            'Client Type',
            'Branch Name',
            'Contact Number',
            'Project',
            'Amount',
            'Quotation Number',
            'Quotation Type',
            'Forecast Close Date',
            'Min. Forecast',
            'Max Forecast',
            'Hit?',
            'Remark Next Step',
            'Close Deal Date',
            'Start Date',
            'End Date',
            'Duration',
            'Created By',
            'Created At'
        ];
        $header     = $booked ? array_merge($header, ['Booked At (the date filter was based on this)']) : $header;
        $filename   = $booked ? 'Booked Task Leads' : 'Task Leads';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }
    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function customerSupports($filters = [], $booked = false)
    {
        $model      = new CustomerSupportModel();
        $columns    = "
            {$model->table}.status,
            {$model->table}.id,
            {$model->view}.client_name,
            {$model->view}.client_branch_name,
            {$model->table}.ticket_number,
            {$model->table}.issue,
            {$model->table}.findings,
            {$model->table}.action,
            {$model->table}.troubleshooting,
            IF({$model->table}.security_ict_system = 'OTHER', {$model->table}.security_ict_system_other, {$model->table}.security_ict_system) AS security_ict_system,
            {$model->table}.priority,
            ".dt_sql_date_format("{$model->table}.due_date")." AS due_date,
            ".dt_sql_date_format("{$model->table}.follow_up_date")." AS follow_up_date,
            {$model->table}.remarks,
            {$model->view}.specialists,
            {$model->view}.created_by,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at,
            {$model->view}.done_by,
            ".dt_sql_datetime_format("{$model->table}.done_at")." AS done_at,
            {$model->view}.turn_over_by,
            ".dt_sql_datetime_format("{$model->table}.turn_over_at")." AS turn_over_at
        ";

        $builder = $model->select($columns);

        // Join with views
        $model->joinView($builder);

        $builder->where("{$model->table}.deleted_at IS NULL");
        $builder->orderBy("{$model->table}.id", 'DESC');

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters);

        $query      = $builder->orderBy('id', 'ASC')->get();
        $data       = $query->getResultArray();
        $header     = [
            'Status',
            'ID #',
            'Client',
            'Client Branch',
            'Ticket Number',
            'Security and ICT System',
            'Priority',
            'Due Date',
            'Follow Up Date',
            'Problem/Issue',
            'Findings',
            'Initial Action Taken',
            'Troubleshooting Done',
            'Remarks',
            'Support Specialist/s',
            'Created By',
            'Created At',
            'Done By',
            'Done At',
            'Turn Over By',
            'Turn Over At'
        ];
        $filename   = 'Customer Supports';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename, function($data, $output) {
            $i      = 0;
            $status = get_customer_support_status();

            while (isset($data[$i])) {
                $row = $data[$i];
                
                if (isset($row['status'])) {
                    $row['status'] = $status[$row['status']];
                }

                $row['specialists'] = str_replace(',', ', ', $row['specialists']);

                fputcsv($output, $row);
                
                $i++;
            }
        });
    }
}