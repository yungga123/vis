<?php

namespace App\Traits;
use App\Models\ScheduleModel;

trait AdminTrait
{
    /**
     * Searching booked taskleads by quotation
     *
     * @param object $model     The model to search
     * @param string $q         The query to search for
     * @param string $options   Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function findBookedTaskLeadsByQuotation($model, $q, $options = [], $fields = '')
    {
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
                    $start  = date('M d, Y h:i A', strtotime($schedule['start']));
                    $end    = date('M d, Y h:i A', strtotime($schedule['end']));
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
}