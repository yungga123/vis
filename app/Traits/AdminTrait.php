<?php

namespace App\Traits;

use App\Models\TaskLeadView;
use App\Models\ScheduleModel;
use App\Models\JobOrderModel;
use App\Models\CustomerModel;
use App\Models\CustomerBranchModel;

trait AdminTrait
{
    /**
     * Searching booked taskleads by quotation
     *
     * @param string $q         The query to search for
     * @param string $options   Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function findBookedTaskLeadsByQuotation($q, $options = [], $fields = '')
    {
        $model  = new TaskLeadView();
        $fields = $fields ? $fields : '
            id,
            employee_id,
            quotation_num as quotation,
            employee_name as manager,
            customer_name as client,
            project,
            tasklead_type as type,
            project_start_date,
            project_finish_date,
            project_duration,
            project_amount
        ';
        
        $model->select($fields);

        if (! empty($q)) {
            if (empty($options)) {                
                $model->where('quotation_num', $q);
                
                return $model->find();
            }

            if (is_numeric($q)) {
                $model->like('id', $q);
            } else {
                $search_in = $options['search_in'] ?? [];

                if (! empty($search_in)) {
                    $arr = [
                        'quotation' => 'quotation_num',
                        'manager'   => 'employee_name',
                        'client'    => 'customer_name',
                        'type'      => 'tasklead_type',
                    ];

                    foreach ($search_in as $key => $val) {
                        $like = $key === 0 ? 'like' : 'orLike';
                        
                        $model->{$like}($arr[$val] ?? $val, $q);
                    }
                } else {
                    $model->like('quotation_num', $q);
                }
            }
            
        }

        $model->orderBy('id', 'DESC');

        $result = $model->paginate($options['perPage'], 'default', $options['page']);
        $total  = $model->countAllResults();

        return [
            'data'  => $result,
            'total' => $total
        ];     
    }

    /**
     * Schedule type legend html formatted
     *
     * @return string   The html formatted legend
     */
    public function scheduleTypeLegend()
    {
        $html   = '';
        $types  = get_schedule_type();

        foreach ($types as $key => $val) {
            $html .= <<<EOF
                <div class="external-event text-white" style="background-color: {$val['color']};">{$val['text']}</div>
            EOF;
        }

        return $html;
    }

    /**
     * Get the current schedules
     * 
     * @param bool $inTableHtml     If true, format schedules and display in table
     * @return array|string         Ethier in table format or the query results
     */
    public function getSchedulesForToday($inTableHtml = false)
    {
        $model      = new ScheduleModel();
        $schedules  = $model->getSchedulesForToday();

        if (! $inTableHtml) return $schedules;

        /* Format schedules and display in table html */
        if (! empty($schedules)) {
            $tbody = '';
            foreach ($schedules as $schedule) {
                $type   = get_schedule_type($schedule['type']);
                $start  = format_datetime($schedule['start']);
                $end    = format_datetime($schedule['end']);
                $tbody .= <<<EOF
                    <tr class="text-white text-bold" style="background-color: {$type['color']};">
                        <td>{$schedule['title']}</td>
                        <td>{$schedule['description']}</td>
                        <td>{$start}</td>
                        <td>{$end}</td>
                        <td>{$type['text']}</td>
                    </tr>
                EOF;
            }
        } else $tbody = '<tr><td colspan="5"><h3>NO SCHEDULES FOR TODAY</h3></td></tr>';

        return <<<EOF
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Start Date & Time</th>
                        <th>End Date & Time</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>{$tbody}</tbody>
            </table>
        EOF;
    }

    /**
     * Get the schedules
     * 
     * @param string $q         The query to search for
     * @param string $options   Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function fetchSchedules($q, $options = [], $fields = '')
    {
        $model  = new ScheduleModel();
        $fields = $fields ? $fields : 'id, title, description, type, start, end';

        $model->select($fields);
        $model->where("{$model->table}.deleted_at IS NULL");

        if (! empty($q)) {
            if (empty($options)) return $model->find($q);

            $model->like('id', $q);
            $model->orLike('title', $q);
            $model->orLike('description', $q);
        }

        if (isset($options['from_jo_only']))
            $model->where("{$model->table}.job_order_id > 0");

        $model->orderBy('id', 'DESC');
        $result = $model->paginate($options['perPage'], 'default', $options['page']);
        $total = $model->countAllResults();

        return [
            'data'  => $result,
            'total'  => $total
        ];
    }

    /**
     * Get the job orders
     * 
     * @param string $q         The query to search for
     * @param string $options   Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function fetchJobOrders($q, $options = [], $fields = '')
    {
        $model  = new JobOrderModel();
        $fields = $fields ? $fields : "
            {$model->view}.job_order_id AS id,
            CONCAT({$model->view}.job_order_id, ' | ', {$model->view}.client_name) AS text,
            {$model->view}.client_name,
            {$model->view}.client_id,
            {$model->view}.quotation,
            {$model->view}.quotation_type,
            {$model->view}.manager
        ";

        $model->setTable($model->view)->select($fields);
        $model->setUseSoftDeletes(false);

        if (! empty($q)) {
            if (empty($options)) return $model->find($q);

            $model->like("{$model->view}.job_order_id", $q);
            $model->orLike("{$model->view}.client_name", $q);
        }

        $model->orderBy("{$model->view}.job_order_id", 'DESC');

        $result = $model->paginate($options['perPage'] ?? 10, 'default', $options['page'] ?? 1);
        $total  = $model->countAllResults();

        return [
            'data'  => $result,
            'total'  => $total
        ];
    }

    /**
     * Fetch customers
     * 
     * @param string $q         The query to search for
     * @param string $options   Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function fetchCustomers($q, $options = [], $fields = '')
    {
        $model  = new CustomerModel();
        $type   = strtoupper($options['customer_type']);
        $fields = $fields ? $fields : "{$model->table}.id, {$model->table}.name AS text";

        $model->select($fields);
        $model->join('customer_branches AS cb', "cb.customer_id = {$model->table}.id", 'left');
        $model->where('type', $type);
        $model->where("{$model->table}.deleted_at IS NULL");

        if (! empty($q)) {
            if (empty($options)) return $model->find($q);

            if (is_numeric($q)) {
                $model->where("{$model->table}.id", $q);
            } else {
                $model->like("LOWER({$model->table}.name)", strtolower($q));
            }
        }

        $model->groupBy("{$model->table}.id, {$model->table}.name");
        $model->orderBy("{$model->table}.id", 'DESC');

        $result = $model->paginate($options['perPage'], 'default', $options['page']);
        $total  = $model->countAllResults();

        return [
            'data'  => $result,
            'total' => $total
        ];
    }

    /**
     * Fetch customer branches either all or via customer id
     * 
     * @param string $q         The query to search for
     * @param string $options   Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function fetchCustomerBranches($q, $options = [], $fields = '')
    {
        $model  = new CustomerBranchModel();
        $fields = $fields ? $fields : 'id, branch_name AS text';

        $model->select($fields);
        $model->where('customer_id', $options['customer_id']);
        $model->where('deleted_at IS NULL');

        if (isset($options['not_select2_ajax'])) {
            return json_encode(['data' => $model->findAll()]);
        }

        if (! empty($q)) {
            if (empty($options)) return $model->find($q);

            $model->like('LOWER(branch_name)', strtolower($q));
        }
        
        $model->orderBy('branch_name', 'ASC');
        $result = $model->paginate($options['perPage'], 'default', $options['page']);
        $total  = $model->countAllResults();

        return [
            'data'  => $result,
            'total' => $total
        ];
    }
}