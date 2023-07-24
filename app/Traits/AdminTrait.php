<?php

namespace App\Traits;

trait AdminTrait
{
    /* Searching booked taskleads by quotation */
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

    /* Schedule type legend */
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
}