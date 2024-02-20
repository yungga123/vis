<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;
use App\Traits\FilterParamTrait;

class OvertimeModel extends Model
{
    /* Declare trait here to use */
    use HRTrait, FilterParamTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'overtime';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employee_id',
        'status',
        'date',
        'time_start',
        'time_end',
        'total_hours',
        'with_pay',
        'reason',
        'remark',
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
        'date'   => [
            'rules' => 'required',
            'label' => 'date',
        ],
        'time_start'   => [
            'rules' => 'required',
            'label' => 'time start',
        ],
        'time_end'   => [
            'rules' => 'required',
            'label' => 'time end',
        ],
        'reason'   => [
            'rules' => 'required|max_length[500]',
            'label' => 'reason',
        ],
        'remark'   => [
            'rules' => 'permit_empty|max_length[500]',
            'label' => 'remark',
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
        // Check date and time range whether user
        // has already an overtime request within that date and time.
        // Then if there's any, throw an exception
        $date       = $data['data']['date'];
        $time_start = $data['data']['time_start'];
        $time_end   = $data['data']['time_end'];
        $check      = $this->checkDateTimes($date, $time_start, $time_end);
                
        if (! empty($check)) {
            throw new \Exception('Selected date and times are overlapping with another overtime request.', 1);
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

            throw new \Exception("You can't <strong>{$action}</strong> {$phrase} overtime request!", 1);
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
     * Check whether selected date and time range were already filed
     * 
     * @param string|null $employee_id
     * 
     * @return array|null
     */
    public function checkDateTimes(string $date, string $time_start, string $time_end, $employee_id = null)
    {
        $between    = "('%s' BETWEEN time_start AND time_end OR '%s' BETWEEN time_start AND time_end)";

        $this->select('id, date, time_start, time_end');
        $this->where(sprintf($between, format_time($time_start, 'H:i'), format_time($time_end, 'H:i')));
        $this->where('employee_id', $employee_id ?? session('employee_id'));
        $this->where('date', $date);
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
            ".dt_sql_date_format("{$this->table}.date")." AS date,
            ".dt_sql_time_format("{$this->table}.time_start")." AS time_start,
            ".dt_sql_time_format("{$this->table}.time_end")." AS time_end,
            ".dt_sql_time_format("{$this->table}.total_hours", '%H:%i')." AS total_hours,
            {$this->table}.with_pay,
            {$this->table}.reason,
            {$this->table}.remark,
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
        
        $start_date = $request['params']['start_date'] ?? '';
        $end_date   = $request['params']['end_date'] ?? '';

        if (! empty($start_date) && ! empty($end_date)) {
            $start_date = format_date($start_date, 'Y-m-d');
            $end_date   = format_date($end_date, 'Y-m-d');
            $between    = "{$this->table}.date BETWEEN '%s' AND '%s'";

            $builder->where(sprintf($between, $start_date, $end_date));
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
        $title  = ucfirst($changeTo) .' Overtime';

        return <<<EOF
            onclick="change({$id}, '{$changeTo}', '{$status}', {$with_pay})" title="{$title}"
        EOF;
    }
}
