<?php

namespace App\Services\Mail;

class ScheduleMailService extends BaseMailService implements MailServiceInterface
{
    /**
     * Send the mail notification
     *
     * @param   array $data
     * @param   string|null $module_code
     * 
     * @return  void
     */
    public function send(array $data, ?string $module_code = null): void
    {
        $module_code = $module_code ? $module_code : get_module_codes('schedules');
        if (! $this->isMailNotifEnabled($module_code)) return;

        $module     = 'Schedule';
        $title      = $module .' Created';
        $type       = get_schedule_type($data['type']);
        $info       = [
            'module'    => $module,
            'title'     => $title,
            'details'   => [
                'Schedule #'        => $data['id'],
                'Job Order #'       => empty($data['job_order_id']) ? 'N/A' : $data['job_order_id'],
                'Title'             => $data['title'],
                'Description'       => $data['description'],
                'Schedule Type'     => ucwords($type['text']),
                'Start'             => format_datetime($data['start']),
                'End'               => format_datetime($data['end']),
                'Created By'        => $data['created_by'],
                'Created At'        => format_datetime($data['created_at']),
            ],
        ];

        // Send the mail
        $this->sendMail($info, $title, $module_code);
    }
}