<?php

namespace App\Services\Mail;

use App\Traits\MailTrait;

class AdminMailService
{
    /* Declare trait here to use */
    use MailTrait;

    /**
     * Send the JO mail notification
     *
     * @param   string $data
     * @return  void
     */
    public function sendJOMailNotif($data)
    {
        $module     = 'Job Order';
        $title      = $data['status'] === 'pending' ? $module .' Created' : $module .' Accepted';
        $info       = [
            'module'    => $module,
            'title'     => $title,
            'details'   => [
                'Job Order #'       => $data['id'],
                'Tasklead #'        => $data['tasklead_id'] ? $data['tasklead_id'] : 'N/A',
                'Manual Quotation?' => $data['is_manual'],
                'Quotation #'       => $data['quotation'],
                'Quotation Type'    => empty($data['tasklead_type']) ? 'N/A' : ucwords($data['tasklead_type']),
                'Client'            => $data['client'],
                'Manager'           => empty($data['manager']) ? 'N/A' : $data['manager'],
                'Status'            => ucwords(str_replace('_', ' ', $data['status'])),
                'Date Requested'    => $data['date_requested'],
                'Date Committed'    => empty($data['date_committed']) ? 'N/A' : $data['date_committed'],
                'Requested By'      => $data['requested_by'],
                'Created At'        => $data['created_at'],
            ],
        ];
        $sendTo     = session('email_address');
        $sendName   = session('name');
        $subject    = 'User Notification - ' . $title;
        $body       = $this->mailTemplate($info);

        // Send the mail via SMTP
        $mail = $this->sendSMTPMail($sendTo, $sendName, $subject, $body);
        $this->logInfo($mail, $title, $info, __METHOD__);
    }

    /**
     * Send the Schedule mail notification
     *
     * @param   string $data
     * @return  void
     */
    public function sendScheduleMailNotif($data)
    {
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
        $sendTo     = session('email_address');
        $sendName   = session('name');
        $subject    = 'User Notification - ' . $title;
        $body       = $this->mailTemplate($info);

        // Send the mail via SMTP
        $mail = $this->sendSMTPMail($sendTo, $sendName, $subject, $body);
        $this->logInfo($mail, $title, $info, __METHOD__);
    }
}