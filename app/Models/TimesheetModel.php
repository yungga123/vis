<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;
use App\Traits\PayrollSettingTrait;

class TimesheetModel extends Model
{
    /* Declare trait here to use */
    use HRTrait, PayrollSettingTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'timesheets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employee_id',
        'clock_date',
        'clock_in',
        'clock_out',
        'total_hours',
        'late',
        'overtime',
        'remark',
        'is_manual',
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
            'rules' => 'required',
            'label' => 'employee ID',
        ],
        'clock_date'     => [
            'rules' => 'required|valid_date',
            'label' => 'clock date',
        ],
        'clock_in'   => [
            'rules' => 'if_exist|required',
            'label' => 'clock in',
        ],
        'clock_out'   => [
            'rules' => 'if_exist|required',
            'label' => 'clock out',
        ],
        'total_hours'   => [
            'rules' => 'permit_empty',
            'label' => 'total hours',
        ],
        'remark'   => [
            'rules' => 'if_exist|required|string|max_length[255]',
            'label' => 'remark',
        ],
        'is_manaul'   => [
            'rules' => 'if_exist|required',
            'label' => 'is manaul',
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['checkDate'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['checkDate'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = ['checkIfUserOwnRecord'];
    protected $afterDelete    = [];

    /**
     * Check dates and set the value for created_by before inserting
     */
    protected function checkDate(array $data)
    {
        $clock_date = $data['data']['clock_date'];

        if (isset($data['id'])) {
            $id = $data['id'];

            if (! is_admin()) {
                // Check if from clock in and out
                // In short not manually inserted
                $_model         = clone $this;
                $_fetch         = $_model->where('is_manual = 0')->fetch($id[0]);
                $_clock_date    = $_fetch['clock_date'];

                if (! empty($_fetch) && (! empty($_fetch['clock_out'])) || compare_dates($_clock_date, current_date())) {
                    throw new \Exception(res_lang('restrict.action.change') . " It's from CLOCK IN/OUT.", 1);
                }
            }

            // Add the id in the where clause.
            $this->whereNotIn($this->primaryKey, $id);
        }

        // Check date to whether user
        // has already added a timesheet within that date.
        // Then if there's any, throw an exception.            
        if (! empty($this->checkClockDate($clock_date))) {
            throw new \Exception('You already have a timesheet for '.format_date($clock_date).'!', 1);
        }

        $_clock_in  = $data['data']['clock_in'] ?? null;
        $_clock_out = $data['data']['clock_out'] ?? null;
        $clock_in   = $_clock_in ? strtotime($_clock_in) : $_clock_in;
        $clock_out  = $_clock_out ? strtotime($_clock_out) : $_clock_out;
        $format     = '%H:%i';

        // Get office hours
        $office_hours   = $this->getOfficeHours();
        $oh_time_in     = $office_hours['working_time_in'] ?? null;
        $oh_time_out    = $office_hours['working_time_out'] ?? null;

        // Check whether the clock out is less than the clock in.
        // If yes, throw an exception.
        if ($_clock_in) {
            if ($clock_out && $clock_out <= $clock_in) {
                throw new \Exception('Clock out should not be less than the clock in!', 1);
            }

            if ($_clock_out) {
                // With less 1 hr break
                $break = 1; 
                // If clock out time is less than 1:00 PM,
                // no more 1hr break time to less
                if (
                    $oh_time_in && 
                    (compare_times($_clock_out, '1:00 PM', '<') || compare_times($_clock_in, '12:00 PM', '>'))
                ) {
                    $break = 0; 
                }

                // Get total hours
                $total_hours = get_total_hours($_clock_in, $_clock_out, $break, $format);
                $data['data']['total_hours'] = $total_hours;
            }

            if (! empty($office_hours)) {
                $time_diff  = get_time_diff($oh_time_in, $_clock_in);
                $field      = $time_diff->invert ? 'early_in' : 'late';
                
                $data['data'][$field] = $time_diff->format($format);
            }
        }

        if (! empty($office_hours) && $clock_out) {
            $time_diff  = get_time_diff($oh_time_out, $_clock_out);
            $field      = $time_diff->invert ? 'early_out' : 'overtime';

            $data['data'][$field] = $time_diff->format($format);
        }

        return $data;
    }

    /**
     * Check whether user's own record - if not, throw an exception.
     * Restrict editing or delete other's record.
     */
    protected function checkIfUserOwnRecord(array $data)
    {
        $id     = $data['id'][0];
        
        if (! is_admin()) {
            // Check if from clock in and out
            // In short not manually inserted
            // If yes, throw an error - no more changes allowed
            $_model = clone $this;
            $_model->where('is_manual = 0');
            $_fetch = $_model->fetch($id);
    
            if (! empty($_fetch)) {
                throw new \Exception(res_lang('restrict.action.change') . " It's from CLOCK IN/OUT.", 1);
            }
        }

        // Check if the to be deleted record is user's own
        // If not, throw an error
        $this->where('employee_id', session('employee_id'));
        
        $action = isset($data['purge']) ? ACTION_DELETE : ACTION_EDIT;
        $record = $this->fetch($id, 'employee_id');

        if (empty($record)) {
            throw new \Exception("You can't <strong>{$action}</strong> other's timesheet!", 1);
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
        $this->where("{$this->table}.deleted_at IS NULL");

        if (! empty($id)) {
            $this->where("{$this->table}.{$field}", $id);
        }

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
     * Check whether selected clock dates were already existed
     * 
     * @param string|null $employee_id
     */
    public function checkClockDate(string|array $clock_date, $employee_id = null): array|null
    {
        $where = is_array($clock_date) ? 'whereIn' : 'where';
        $fecth = is_array($clock_date) ? 'findAll' : 'first';

        $this->select('id, clock_date');
        $this->{$where}('clock_date', format_date($clock_date, 'Y-m-d'));
        $this->where('employee_id', $employee_id ?? session('employee_id'));
        $this->where('deleted_at IS NULL');

        return $this->{$fecth}();
    }

    /**
     * For DataTable
     */
    public function noticeTable(array $request, $permissions): object
    {
        $model      = new EmployeeViewModel();
        $columns    = "
            {$this->table}.id,
            {$this->table}.employee_id,
            {$model->table}.employee_name,
            ".dt_sql_date_format("{$this->table}.clock_date")." AS clock_date,
            ".dt_sql_time_format("{$this->table}.clock_in")." AS clock_in,
            ".dt_sql_time_format("{$this->table}.clock_out")." AS clock_out,
            ".dt_sql_time_format("{$this->table}.total_hours", '%H:%i')." AS total_hours,
            ".dt_sql_time_format("{$this->table}.early_in", '%H:%i')." AS early_in,
            ".dt_sql_time_format("{$this->table}.late", '%H:%i')." AS late,
            ".dt_sql_time_format("{$this->table}.early_out", '%H:%i')." AS early_out,
            ".dt_sql_time_format("{$this->table}.overtime", '%H:%i')." AS overtime,
            IF({$this->table}.is_manual != 0, 'Manual', 'Clock In/Out') AS clock_type,
            {$this->table}.remark,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at
        ";
        $builder    = $this->db->table($this->table);

        $builder->select($columns);

        $this->traitJoinEmployees($builder, 'employee_id', "{$this->table}.employee_id", '', 'left', true);

        // Not include dev record
        if (! is_developer()) {
            $builder->where("{$this->table}.employee_id !=", DEVELOPER_ACCOUNT);
        }

        if (! in_array(ACTION_VIEW_ALL, $permissions)) {
            $builder->where("{$this->table}.employee_id", session('employee_id'));
        }
        
        $start_date = $request['params']['start_date'] ?? '';
        $end_date   = $request['params']['end_date'] ?? '';

        if (! empty($start_date) && ! empty($end_date)) {
            $between = "{$this->table}.clock_date BETWEEN '%s' AND '%s'";
            $builder->where(sprintf($between, format_date($start_date, 'Y-m-d'), format_date($end_date, 'Y-m-d')));
        }

        if (! empty($request['params']['view'] ?? '')) {
            $builder->where("{$this->table}.employee_id", session('employee_id'));
        }

        $builder->where("{$this->table}.deleted_at IS NULL");
        $builder->orderBy("{$this->table}.clock_date", 'DESC');

        return $builder;
    }
 
    /**
     * DataTable action buttons
     */
    public function buttons(array $permissions)
    {
        $id         = $this->primaryKey;
        $closureFun = function($row) use($id, $permissions) {
            $employee_id    = $row['employee_id'];
            $buttons        = dt_button_actions($row, $id, $permissions);

            return session('employee_id') === $employee_id
                ? $buttons : '~~N/A~~';
        };
        
        return $closureFun;
    }
}
