<?php

namespace App\Services\Mail;

use App\Traits\MailTrait;

class InventoryMailService
{
    /* Declare trait here to use */
    use MailTrait;

    /**
     * Send the PRF mail notification
     *
     * @param   string $data
     * @return  void
     */
    public function sendPrfMailNotif($data)
    {
        $module     = 'Project Request Form';
        $title      = $data['status'] === 'pending' ? $module .' Created' : $module .' Accepted';
        $url        = url_to('prf.home') . '?id='. $data['id'] .'&mail=true';
        $items      = '<a href="'.$url.'">Click here</a>';
        $info       = [
            'module'    => $module,
            'title'     => $title,
            'details'   => [
                'RPF #'             => $data['id'],
                'Job Order #'       => $data['job_order_id'],
                'Status'            => ucwords(str_replace('_', ' ', $data['status'])),
                'Process Date'      => format_date($data['process_date']),
                'Created By'        => $data['created_by'],
                'Created At'        => format_datetime($data['created_at']),
                'Items'             => $items,
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