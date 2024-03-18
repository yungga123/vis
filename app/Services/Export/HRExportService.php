<?php

namespace App\Services\Export;

use App\Models\AccountModel;
use App\Models\EmployeeModel;
use App\Models\EmployeeViewModel;

class HRExportService extends ExportService
{
    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function accounts($filters = [])
    {
        $model      = new AccountModel();
        $columns    = "
            {$model->table}.employee_id,
            {$model->view}.employee_name,
            {$model->table}.username,
            UPPER({$model->table}.access_level) AS role_code,
            {$model->view}.created_by_name,
            DATE_FORMAT({$model->table}.created_at, '".dt_sql_datetime_format()."') AS created_at_formatted
        ";
        $builder    = $model->select($columns);
        $builder->join($model->view, "{$model->table}.account_id = {$model->view}.id", 'left');
        $builder->orderBy("{$model->view}.employee_name", 'ASC');

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters, 'type');

        $data       = $builder->findAll();
        $header     = [
            'Employee ID',
            'Employee Name',
            'Username',
            'Role',
            'Created By',
            'Created At',
        ];
        $filename   = 'Accounts Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename, function($data, $output) {
            $i      = 0;
            $roles  = get_roles();
            while (isset($data[$i])) {
                $row = $data[$i];
                
                if (isset($row['role_code']))
                    $row['role_code'] = $roles[$row['role_code']];

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
    public function employees($filters = [])
    {
        $model      = new EmployeeModel();
        $modelV     = new EmployeeViewModel();
        $builder    = $modelV->select($model->dtColumns);
        $builder->orderBy('employee_name', 'ASC');

        // Process and add filters
        $this->processFilters($model->view, $builder, $filters, 'employment_status');

        $data       = $builder->findAll();
        $header     = [
            'Employee ID',
            'Employee Name',
            'Address',
            'Gender',
            'Civil Status',
            'Birthdate',
            'Birthplace',
            'Position',
            'Employment Status',
            'Date Hired',
            'Date Resigned',
            'Contact Number',
            'Email Address',
            'SSS Number',
            'TIN Number',
            'PhilHealth Number',
            'PAGIBIG Number',
            'Educational Attainment',
            'Course',
            'Emergency Name',
            'Emergency Contact Number',
            'Emergency Address',
            'Spouse Name',
            'Spouse Contact Number',
            'No. of Children',
            'Spouse Address',
            'Created By',
            'Created At',
        ];
        $filename   = 'Employees Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }
}