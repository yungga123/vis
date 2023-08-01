<?php

namespace App\Traits;

use App\Models\TaskLeadView;
use App\Models\ScheduleModel;
use App\Models\CustomersVtModel as ClientCommercial;
use App\Models\CustomersResidentialModel as ClientResidential;

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

            $model->like('quotation_num', $q);            
        }

        $result = $model->paginate($options['perPage'], 'default', $options['page']);
        $total = $model->countAllResults();

        return [
            'data'  => $result,
            'total'  => $total
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

        if ($inTableHtml) {
            /* Format schedules and display in table html */
            if (!empty($schedules)) {
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
                        </tr>
                    EOF;
                }
            } else $tbody = '<tr><td colspan="4"><h3>NO SCHEDULES FOR TODAY</h3></td></tr>';
    
            return <<<EOF
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Start Date & Time</th>
                            <th>End Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>{$tbody}</tbody>
                </table>
            EOF;
        } 
        else return $schedules; 
    }

    /**
     * Get the current schedules
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

        if (! empty($q)) {
            if (empty($options)) return $model->find($q);

            $model->like('id', $q);
            $model->orLike('title', $q);
            $model->orLike('description', $q);
        }

        $result = $model->paginate($options['perPage'], 'default', $options['page']);
        $total = $model->countAllResults();

        return [
            'data'  => $result,
            'total'  => $total
        ];
    }

    /**
     * Get the current schedules
     * 
     * @param string $q         The query to search for
     * @param string $options   Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function fetchCustomers($q, $options = [], $fields = '')
    {
        if ($options['customer_type'] === 'residential') $model = new ClientResidential();
        else $model = new ClientCommercial();

        $fields = $fields ? $fields : 'id, customer_name AS text';

        $model->select($fields);

        if (! empty($q)) {
            if (empty($options)) return $model->find($q);

            $model->like('LOWER(customer_name)', strtolower($q));
        }

        $result = $model->paginate($options['perPage'], 'default', $options['page']);
        $total  = $model->countAllResults();

        return [
            'data'  => $result,
            'total' => $total
        ];
    }
}