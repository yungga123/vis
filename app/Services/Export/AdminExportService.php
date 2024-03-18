<?php

namespace App\Services\Export;

use App\Models\DispatchModel;
use App\Models\ScheduleModel;
use App\Models\CustomerModel;
use App\Models\JobOrderModel;

class AdminExportService extends ExportService
{
    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function dispatch($filters = [])
    {
        $model          = new DispatchModel();        
        $scheduleModel  = new ScheduleModel();
        $customerModel  = new CustomerModel();
        $columns        = "
            {$model->table}.id,
            {$model->table}.schedule_id,
            {$scheduleModel->table}.title,
            {$customerModel->table}.name AS client,
            {$customerModel->table}.type AS client_type,
            ".dt_sql_date_format("{$model->table}.dispatch_date")." AS dispatch_date,
            ".dt_sql_time_format("{$model->table}.dispatch_out")." AS dispatch_out,
            ".dt_sql_time_format("{$model->table}.time_in")." AS time_in,
            ".dt_sql_time_format("{$model->table}.time_out")." AS time_out,
            {$model->table}.sr_number,
            {$model->view}.technicians,
            {$model->table}.service_type,
            {$model->table}.with_permit,
            {$model->table}.comments,
            {$model->table}.remarks,
            {$model->view}.checked_by_name,
            {$model->view}.dispatched_by,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS dispatched_at
        ";
        $builder    = $model->select($columns);

        // Join with other tables
        $model->joinView($builder);
        $model->joinSchedule($builder);
        $model->joinCustomer($builder);

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters);
        $builder->orderBy("{$model->table}.id", 'ASC');

        $data       = $builder->findAll();
        $header     = [
            'Dispatch ID',
            'Schedule ID',
            'Schedule Title',
            'Client',
            'Client Type',
            'Dispatch Date',
            'Dispatch Out',
            'Time In',
            'Time Out',
            'SR Number',
            'Technicians',
            'Service Type',
            'With Permit',
            'Comments',
            'Remarks',
            'Checked By',
            'Dispatched By',
            'Dispatched At'
        ];
        $filename   = 'Dispatch Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename, function($data, $output) {
            $i          = 0;
            $services   = get_dispatch_services();
            while (isset($data[$i])) {
                $row = $data[$i];
                
                if (isset($row['service_type']))
                    $row['service_type'] = $services[$row['service_type']];

                fputcsv($output, $row);
                $i++;
            }
        });
    }

    /**
     * Exporting data to csv 
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function jobOrders($filters = [])
    {
        $model      = new JobOrderModel();
        $builder    = $model->select($model->dtColumns());

        $model->joinWithOtherTables($builder, true);
        
        // Process and add filters
        $this->processFilters($model->table, $builder, $filters);
        $builder->orderBy("{$model->table}.id", 'ASC');

        $data       = $builder->findAll();
        $header     = [
            'Status',
            'JO #',
            'Task Lead #',
            'Is Manual Quotation',
            'Quotation',
            'Quotation Type',
            'Client Type',
            'Client',
            'Client Branch',
            'Manager',
            'Work Type',
            'Date Requested',
            'Date Committed',
            'Date Reported',
            'Warranty',
            'Comments',
            'Remarks',
            'Requested By',
            'Requested At',
            'Accepted By',
            'Accepted At',
            'Filed By',
            'Filed At',
            'Discarded By',
            'Discarded At',
            'Reverted By',
            'Reverted At'
        ];
        $filename   = 'Job Orders Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv 
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function schedules($filters = [])
    {
        $model      = new ScheduleModel();
        $columns    = "
            {$model->table}.job_order_id,
            {$model->table}.title,
            {$model->table}.description,
            {$model->table}.type,
            ".dt_sql_datetime_format("{$model->table}.start")." AS start,
            ".dt_sql_datetime_format("{$model->table}.end")." AS end,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at
        ";
        $builder    = $model->select($columns);

        $this->joinAccountView($builder, "{$model->table}.created_by", 'cb');
        
        // Process and add filters
        $this->processFilters($model->table, $builder, $filters, 'type');
        $builder->orderBy("{$model->table}.id", 'ASC');

        $data       = $builder->findAll();
        $header     = [
            'JO #',
            'Schedule Title',
            'Schedule Description',
            'Schedule Type',
            'Schedule Start At',
            'Schedule End At',
            'Created By',
            'Created At',
        ];
        $filename   = 'Schedules Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename, function($data, $output) {
            $i      = 0;
            $types  = get_schedule_type();
            while (isset($data[$i])) {
                $row = $data[$i];
                
                if (isset($row['type']))
                    $row['type'] = $types[$row['type']]['text'];

                fputcsv($output, $row);
                $i++;
            }
        });
    }
}