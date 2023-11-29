<?php

namespace App\Services\Mail;

class SalesMailService extends BaseService
{
    /**
     * Send the Tasklead mail notification
     *
     * @param   array $data
     * @return  void
     */
    public function sendTaskleadMailNotif($data)
    {
        $module     = 'Task Lead';
        $title      = $module .' Booked';
        $info       = [
            'module'    => $module,
            'title'     => $title,
            'details'   => [
                'Task Lead ID'          => $data['id'],
                'Percent'               => $data['status'],
                'Hit?'                  => $data['hit_or_missed'],
                'Quotation #'           => $data['quotation_num'],
                'Quotation Type'        => $data['tasklead_type'],
                'Manger'                => $data['manager'],
                'Client'                => $data['client'],
                'Client Type'           => $data['client_type'],
                'Client Branch'         => $data['client_branch'],
                'Quarter'               => $data['quarter'],
                'Project'               => $data['project'],
                'Project Amount'        => number_format($data['project_amount'], 2),
                'Close Deal Date'       => empty($data['close_deal_date']) ? 'N/A' : format_date($data['close_deal_date']),
                'Project Start Date'    => empty($data['project_start_date']) ? 'N/A' : format_date($data['project_start_date']),
                'Project Finish Date'   => empty($data['project_finish_date']) ? 'N/A' : format_date($data['project_finish_date']),
                'Project Duration'      => empty($data['project_duration']) ? 'N/A' : strtoupper($data['project_duration']),
                'Remarks'               => $data['remark_next_step'],
                'Booked By'             => session('name'),
                'Booked At'             => format_datetime(current_datetime()),
            ],
        ];

        // Send the mail
        $this->sendMail($info, $title);
    }
}