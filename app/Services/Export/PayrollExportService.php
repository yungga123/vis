<?php

namespace App\Services\Export;

use App\Models\EmployeeViewModel;
use App\Models\LeaveModel;
use App\Models\OvertimeModel;
use App\Models\PayrollModel;
use App\Models\SalaryRateModel;
use App\Models\TimesheetModel;
use App\Traits\HRTrait;

class PayrollExportService extends ExportService
{
    /* Declare trait here to use */
    use HRTrait;

    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function leave($filters = [])
    {
        $model      = new LeaveModel();
        $empVModel  = new EmployeeViewModel();
        $columns    = "
            UPPER({$model->table}.status) AS status,
            {$model->table}.employee_id,
            {$empVModel->table}.employee_name,
            {$model->table}.leave_type,
            ".dt_sql_date_format("{$model->table}.start_date")." AS start_date,
            ".dt_sql_date_format("{$model->table}.end_date")." AS end_date,
            {$model->table}.total_days,
            {$model->table}.leave_reason,
            {$model->table}.leave_remark,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at,
            pb.employee_name AS processed_by,
            ".dt_sql_datetime_format("{$model->table}.processed_at")." AS processed_at,
            ab.employee_name AS approved_by,
            ".dt_sql_datetime_format("{$model->table}.approved_at")." AS approved_at,
            db.employee_name AS discarded_by,
            ".dt_sql_datetime_format("{$model->table}.discarded_at")." AS discarded_at
        ";
        $builder    = $model->select($columns);

        $this->traitJoinEmployees($builder, 'employee_id', "{$model->table}.employee_id", '', 'left', true);
        $this->joinAccountView($builder, 'processed_by', 'pb');
        $this->joinAccountView($builder, 'approved_by', 'ab');
        $this->joinAccountView($builder, 'discarded_by', 'db');

        // Not include dev record
        if (! is_developer()) {
            $builder->where("{$model->table}.employee_id !=", DEVELOPER_ACCOUNT);
        }

        $permissions = $filters['permissions'] ?? [];

        if (
            (! in_array(ACTION_VIEW_ALL, $permissions) && ! is_admin()) ||
            ($filters['status'] ?? '') === 'mine'
        ) {
            $builder->where("{$model->table}.employee_id", session('employee_id'));
        }

        if (in_array(($filters['status'] ?? ''), ['mine', 'all'])) {
            unset($filters['status']);
        }

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters);

        $builder->where("{$model->table}.deleted_at IS NULL");
        $builder->orderBy("{$model->table}.id", 'DESC');

        $data       = $builder->findAll();
        $header     = [
            'Status',
            'Employee ID',
            'Employee Name',
            'Leave Type',
            'Start Date',
            'End Date',
            'Total Day/s',
            'Leave Reason',
            'Leave Remarks',
            'Filed At',
            'Processed By',
            'Processed At',
            'Approved By',
            'Approved At',
            'Discarded By',
            'Discarded At',
        ];
        $filename   = 'Leave Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function overtime($filters = [])
    {
        $model      = new OvertimeModel();
        $empVModel  = new EmployeeViewModel();
        $columns    = "
            UPPER({$model->table}.status) AS status,
            {$model->table}.employee_id,
            {$empVModel->table}.employee_name,
            ".dt_sql_date_format("{$model->table}.date")." AS date,
            ".dt_sql_time_format("{$model->table}.time_start")." AS time_start,
            ".dt_sql_time_format("{$model->table}.time_end")." AS time_end,
            ".dt_sql_time_format("{$model->table}.total_hours", '%H:%i')." AS total_hours,
            {$model->table}.reason,
            {$model->table}.remark,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at,
            pb.employee_name AS processed_by,
            ".dt_sql_datetime_format("{$model->table}.processed_at")." AS processed_at,
            ab.employee_name AS approved_by,
            ".dt_sql_datetime_format("{$model->table}.approved_at")." AS approved_at,
            db.employee_name AS discarded_by,
            ".dt_sql_datetime_format("{$model->table}.discarded_at")." AS discarded_at
        ";
        $builder    = $model->select($columns);

        $this->traitJoinEmployees($builder, 'employee_id', "{$model->table}.employee_id", '', 'left', true);
        $this->joinAccountView($builder, 'processed_by', 'pb');
        $this->joinAccountView($builder, 'approved_by', 'ab');
        $this->joinAccountView($builder, 'discarded_by', 'db');

        // Not include dev record
        if (! is_developer()) {
            $builder->where("{$model->table}.employee_id !=", DEVELOPER_ACCOUNT);
        }

        $permissions = $filters['permissions'] ?? [];

        if (
            (! in_array(ACTION_VIEW_ALL, $permissions) && ! is_admin()) ||
            ($filters['status'] ?? '') === 'mine'
        ) {
            $builder->where("{$model->table}.employee_id", session('employee_id'));
        }

        if (in_array(($filters['status'] ?? ''), ['mine', 'all'])) {
            unset($filters['status']);
        }

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters);

        $builder->where("{$model->table}.deleted_at IS NULL");
        $builder->orderBy("{$model->table}.id", 'DESC');

        $data       = $builder->findAll();
        $header     = [
            'Status',
            'Employee ID',
            'Employee Name',
            'Date',
            'Time Start',
            'Time End',
            'Total Hour/s',
            'Reason',
            'Remarks',
            'Filed At',
            'Processed By',
            'Processed At',
            'Approved By',
            'Approved At',
            'Discarded By',
            'Discarded At',
        ];
        $filename   = 'Overtime Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function payslip($filters = [])
    {
        $model      = new PayrollModel();
        $empVModel  = new EmployeeViewModel();
        $columns    = "
            {$model->table}.id,
            {$model->table}.employee_id,
            {$empVModel->table}.employee_name,
            {$empVModel->table}.position,
            ".dt_sql_date_format("{$model->table}.cutoff_start")." AS cutoff_start,
            ".dt_sql_date_format("{$model->table}.cutoff_end")." AS cutoff_end,
            ".dt_sql_number_format("{$model->table}.gross_pay")." AS gross_pay,
            ".dt_sql_number_format("{$model->table}.net_pay")." AS net_pay,
            ".dt_sql_number_format("{$model->table}.cutoff_pay")." AS cutoff_pay,
            {$model->table}.salary_type,
            CONCAT({$model->table}.working_days, ' Days') AS working_days,
            {$model->table}.notes,
            cb.employee_name AS processed_by,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS processed_at
        ";
        $builder    = $model->select($columns);

        $this->traitJoinEmployees($builder, 'employee_id', "{$model->table}.employee_id", '', 'left', true);
        $this->joinAccountView($builder, 'created_by', 'cb');

        // Not include dev record
        if (! is_developer()) {
            $builder->where("{$model->table}.employee_id !=", DEVELOPER_ACCOUNT);
        }

        $permissions = $filters['permissions'] ?? [];

        if (
            (! in_array(ACTION_VIEW_ALL, $permissions) && ! is_admin()) ||
            ($filters['status'] ?? '') === 'mine'
        ) {
            $builder->where("{$model->table}.employee_id", session('employee_id'));
        }

        if (in_array(($filters['status'] ?? ''), ['mine', 'all'])) {
            unset($filters['status']);
        }

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters);

        $builder->where("{$model->table}.deleted_at IS NULL");
        $builder->orderBy("{$model->table}.id", 'DESC');

        $data       = $builder->findAll();
        $header     = [
            'Payroll #',
            'Employee ID',
            'Employee Name',
            'Position',
            'Cut-Off Start Date',
            'Cut-Off End Date',
            'Cut-Off Pay',
            'Gross Pay',
            'Net Pay',
            'Salary Type',
            'Working Days',
            'Notes',
            'Processed By',
            'Processed At',
        ];
        $filename   = 'Payslip Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function timesheets($filters = [])
    {
        $model      = new TimesheetModel();
        $empVModel  = new EmployeeViewModel();
        $columns    = "
            {$model->table}.employee_id,
            {$empVModel->table}.employee_name,
            ".dt_sql_date_format("{$model->table}.clock_date")." AS clock_date,
            ".dt_sql_time_format("{$model->table}.clock_in")." AS clock_in,
            ".dt_sql_time_format("{$model->table}.clock_out")." AS clock_out,
            ".dt_sql_time_format("{$model->table}.total_hours", '%H:%i')." AS total_hours,
            ".dt_sql_time_format("{$model->table}.early_in", '%H:%i')." AS early_in,
            ".dt_sql_time_format("{$model->table}.late", '%H:%i')." AS late,
            ".dt_sql_time_format("{$model->table}.early_out", '%H:%i')." AS early_out,
            ".dt_sql_time_format("{$model->table}.overtime", '%H:%i')." AS overtime,
            IF({$model->table}.is_manual != 0, 'Manual', 'Clock In/Out') AS clock_type,
            {$model->table}.remark,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at
        ";
        $builder    = $model->select($columns);

        $this->traitJoinEmployees($builder, 'employee_id', "{$model->table}.employee_id", '', 'left', true);

        // Not include dev record
        if (! is_developer()) {
            $builder->where("{$model->table}.employee_id !=", DEVELOPER_ACCOUNT);
        }

        $permissions = $filters['permissions'] ?? [];

        if (
            (! in_array(ACTION_VIEW_ALL, $permissions) && ! is_admin()) ||
            ($filters['status'] ?? '') === 'mine'
        ) {
            $builder->where("{$model->table}.employee_id", session('employee_id'));
        }

        if (in_array(($filters['status'] ?? ''), ['mine', 'all'])) {
            unset($filters['status']);
        }

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters, 'leave_type');

        $builder->where("{$model->table}.deleted_at IS NULL");
        $builder->orderBy("{$model->table}.id", 'DESC');

        $data       = $builder->findAll();
        $header     = [
            'Employee ID',
            'Employee Name',
            'Clock Date',
            'Clock In',
            'Clock Out',
            'Total Hour/s',
            'Early In',
            'Late',
            'Early Out',
            'Overtime',
            'Clock Type',
            'Remarks',
            'Created At',
        ];
        $filename   = 'Timesheets Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function salaryRates($filters = [])
    {
        $model      = new SalaryRateModel();
        $empVModel  = new EmployeeViewModel();
        $columns    = "
            {$model->table}.employee_id,
            {$empVModel->table}.employee_name,
            {$empVModel->table}.position,
            {$empVModel->table}.employment_status,
            {$model->table}.rate_type,
            ".dt_sql_number_format("{$model->table}.salary_rate")." AS salary_rate,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at
        ";
        $builder    = $model->select($columns);

        $this->traitJoinEmployees($builder, 'employee_id', "{$model->table}.employee_id", '', 'left', true);
        $this->joinAccountView($builder, "{$model->table}.created_by", 'cb');

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters, 'leave_type');

        $builder->where("{$model->table}.deleted_at IS NULL");
        $builder->orderBy("{$empVModel->table}.employee_name", 'ASC');

        $data       = $builder->findAll();
        $header     = [
            'Employee ID',
            'Employee Name',
            'Position',
            'Employee Status',
            'Rate Type',
            'Salary Rate',
            'Set By',
            'Set At',
        ];
        $filename   = 'Salary Rates Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }
}