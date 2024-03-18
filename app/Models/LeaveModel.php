<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;
use App\Traits\FilterParamTrait;

class LeaveModel extends Model
{
    /* Declare trait here to use */
    use HRTrait, FilterParamTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'leave';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employee_id',
        'status',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'with_pay',
        'leave_reason',
        'leave_remark',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'employee_id'     => [
            'rules' => 'required|max_length[100]',
            'label' => 'employee name',
        ],
        'status'   => [
            'rules' => 'permit_empty',
            'label' => 'status',
        ],
        'leave_type'   => [
            'rules' => 'required',
            'label' => 'leave type',
        ],
        'start_date'   => [
            'rules' => 'required',
            'label' => 'start date',
        ],
        'end_date'   => [
            'rules' => 'required',
            'label' => 'end date',
        ],
        'total_days'   => [
            'rules' => 'if_exist|required',
            'label' => 'total days',
        ],
        'leave_reason'   => [
            'rules' => 'required|max_length[500]',
            'label' => 'leave reason',
        ],
        'leave_remark'   => [
            'rules' => 'permit_empty|max_length[500]',
            'label' => 'leave remark',
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['checkDatesAndSetCreatedBy'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setStatusByAndAt', 'checkIfUserOwnRecord'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = ['checkIfUserOwnRecord'];
    protected $afterDelete    = [];

    // Custom variables
    // Restrict edit/delete action for this statuses
    protected $restrictedStatuses = ['processed', 'approved', 'discarded'];

    /**
     * Check dates and set the value for created_by before inserting
     */
    protected function checkDatesAndSetCreatedBy(array $data)
    {
        // Check dates to whether user
        // has already a leave request within that date range.
        // Then if there's any, throw an exception
        $start_date     = $data['data']['start_date'];
        $end_date       = $data['data']['end_date'];
        $check_dates    = $this->checkLeaveDates($start_date, $end_date);
                
        if (! empty($check_dates)) {
            throw new \Exception('Selected leave dates are overlapping with another leave request.', 1);
        }

        // Set the value for created_by
        $data['data']['created_by'] = session('username');

        return $data;
    }

    /**
     * Set the value for 'status_' by and at before updating status
     */
    protected function setStatusByAndAt(array $data)
    {
        if (isset($data['data']['status'])) {
            $status = $data['data']['status'];
            $data['data'][$status .'_by'] = session('username');
            $data['data'][$status .'_at'] = current_datetime();
        }
        
        return $data;
    }

    /**
     * Check whether user's own record - if not, throw an exception.
     * Restrict editing or delete other's record.
     */
    protected function checkIfUserOwnRecord(array $data)
    {
        $this->where('employee_id', session('employee_id'));
        
        $id     = $data['id'][0];
        $action = isset($data['purge']) ? ACTION_DELETE : ACTION_EDIT;
        $record = $this->fetch($id, 'employee_id');
        $status = $data['data']['status'] ?? null;

        if ((empty($record) && ! $status) || (! empty($record) && $status)) {
            $action = $status ? strtoupper($status) : $action;
            $phrase = $status ? 'your own' : "other's";

            throw new \Exception("You can't <strong>{$action}</strong> {$phrase} leave request!", 1);
        }

        return $data;
    }

    /**
     * For counting records
     */
    public function countRecords($is_own = false)
    {
        $builder = $this->where('deleted_at IS NULL');

        if ($is_own) {
            $builder->where('employee_id', session('employee_id'));
        }
        
        return $builder->countAllResults();
        
    }

    /**
     * For fetching single data
     * 
     * @param bool $byPKId  Fetch by primary id - default true
     */
    public function fetch(string|int $id, string|array $columns = '', $byPKId = true): array|null
    {
        $columns    = $columns ? $columns :  [$this->primaryKey] + $this->allowedFields;
        $field      = $byPKId ? 'id' : 'employee_id';
        
        $this->select($columns);
        $this->where("{$this->table}.{$field}", $id);
        $this->where("{$this->table}.deleted_at IS NULL");

        return $this->first();
    }

    /**
     * For fetching multiple data
     * 
     * @param string|array $columns
     * @param bool $byPKId  Fetch by primary id - default true
     */
    public function fetchAll(array $id = [], $columns = '', $byPKId = true, int $limit = 0, int $offset = 0): array
    {
        $columns = $columns ? $columns : [$this->primaryKey] + $this->allowedFields;
        
        $this->select($columns);

        if (! empty($id)) {
            $field = $byPKId ? 'id' : 'employee_id';
            $this->whereIn("{$this->table}.{$field}", $id);
        }

        $this->where("{$this->table}.deleted_at IS NULL");

        return $this->findAll($limit, $offset);
    }

    /**
     * Check whether selected leave date range were already filed
     * 
     * @param string|null $employee_id
     */
    public function checkLeaveDates(string $startDate, string $endDate, $employee_id = null): array|null
    {
        $between    = "('%s' BETWEEN start_date AND end_date OR '%s' BETWEEN start_date AND end_date)";

        $this->select('id, start_date, end_date');
        $this->where(sprintf($between, format_date($startDate, 'Y-m-d'), format_date($endDate, 'Y-m-d')));
        $this->where('employee_id', $employee_id ?? session('employee_id'));
        $this->where('status !=', 'discarded');
        $this->where('deleted_at IS NULL');

        return $this->findAll();
    }

    /**
     * For DataTable
     */
    public function noticeTable(array $request, $permissions): object
    {
        $model      = new EmployeeViewModel();
        $columns    = "
            {$this->table}.id,
            {$this->table}.status,
            {$this->table}.employee_id,
            {$model->table}.employee_name,
            {$this->table}.leave_type,
            {$this->table}.with_pay,
            ".dt_sql_date_format("{$this->table}.start_date")." AS start_date,
            ".dt_sql_date_format("{$this->table}.end_date")." AS end_date,
            {$this->table}.total_days,
            {$this->table}.leave_reason,
            {$this->table}.leave_remark,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at,
            pb.employee_name AS processed_by,
            ".dt_sql_datetime_format("{$this->table}.processed_at")." AS processed_at,
            ab.employee_name AS approved_by,
            ".dt_sql_datetime_format("{$this->table}.approved_at")." AS approved_at,
            db.employee_name AS discarded_by,
            ".dt_sql_datetime_format("{$this->table}.discarded_at")." AS discarded_at
        ";
        $builder    = $this->db->table($this->table);

        $builder->select($columns);

        $this->traitJoinEmployees($builder, 'employee_id', "{$this->table}.employee_id", '', 'left', true);
        $this->joinAccountView($builder, 'processed_by', 'pb');
        $this->joinAccountView($builder, 'approved_by', 'ab');
        $this->joinAccountView($builder, 'discarded_by', 'db');

        // Not include dev record
        if (! is_developer()) {
            $builder->where("{$this->table}.employee_id !=", DEVELOPER_ACCOUNT);
        }

        if (! in_array(ACTION_VIEW_ALL, $permissions)) {
            $builder->where("{$this->table}.employee_id", session('employee_id'));
        }
        
        $this->filterParam($request, $builder);
        $this->filterParam($request, $builder, 'leave_type', 'leave_type');
        
        $start_date = $request['params']['start_date'] ?? '';
        $end_date   = $request['params']['end_date'] ?? '';

        if (! empty($start_date) && ! empty($end_date)) {
            $start_date = format_date($start_date, 'Y-m-d');
            $end_date   = format_date($end_date, 'Y-m-d');

            $builder->where("{$this->table}.start_date >=", $start_date);
            $builder->where("{$this->table}.end_date <=", $end_date);
        }

        $builder->where("{$this->table}.deleted_at IS NULL");
        $builder->orderBy("{$this->table}.id", 'DESC');

        return $builder;
    }

    /**
     * DataTable action buttons
     */
    public function buttons(array $permissions)
    {
        $id         = $this->primaryKey;
        $dropdown   = false;
        $closureFun = function($row) use($id, $permissions, $dropdown) {
            $buttons    = dt_button_actions($row, $id, $permissions);
            $status     = $row['status'];
            $with_pay   = $row['with_pay'];

            if ($status === 'pending') {
                if (check_permissions($permissions, 'PROCESS')) {
                    // Process Leave
                    $changeTo = 'process';
                    $buttons .= dt_button_html([
                        'text'      => $dropdown ? ucfirst($changeTo) : '',
                        'button'    => 'btn-primary',
                        'icon'      => 'fas fa-check-circle',
                        'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $status, $with_pay),
                    ], $dropdown);
                }
            }

            if (check_permissions($permissions, 'APPROVED') && $status === 'processed') {
                // Approve Leave
                $changeTo = 'approve';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? ucfirst($changeTo) : '',
                    'button'    => 'btn-success',
                    'icon'      => 'fas fa-check-double',
                    'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $status, $with_pay),
                ], $dropdown);
            }

            if (
                check_permissions($permissions, 'DISCARD') 
                && ! in_array($status, ['approved', 'discarded'])
            ) {
                // Discard Leave
                $changeTo = 'discard';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? ucfirst($changeTo) : '',
                    'button'    => 'btn-secondary',
                    'icon'      => 'fas fa-times-circle',
                    'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $status, $with_pay),
                ], $dropdown);
            }

            return dt_buttons_dropdown($buttons);
        };
        
        return $closureFun;
    }

    /**
     * DataTable status formatter
     */
    public function dtStatusFormat()
    {
        $closureFun = function($row) {
            $text    = ucwords($row['status']);
            $color   = $row['status'] === 'approved' ? 'success' : dt_status_color($row['status']);
            return text_badge($color, $text);
        };
        
        return $closureFun;
    }

    /**
     * For status onchange event
     */
    private function _statusDTOnchange($id, $changeTo, $status, $with_pay)
    {
        $title  = ucfirst($changeTo) .' Leave';

        return <<<EOF
            onclick="change({$id}, '{$changeTo}', '{$status}', {$with_pay})" title="{$title}"
        EOF;
    }
}
