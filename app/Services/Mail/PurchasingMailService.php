<?php

namespace App\Services\Mail;

use App\Traits\MailTrait;

class PurchasingMailService
{
    /* Declare trait here to use */
    use MailTrait;

    /**
     * Send the JO mail notification
     *
     * @param   string $data
     * @return  void
     */
    public function sendRpfMailNotif($data)
    {
        $module     = 'Request to Purchase Form';
        $title      = $data['status'] === 'pending' ? $module .' Created' : $module .' Accepted';
        $url        = url_to('rpf.home') . '?id='. $data['id'] .'&mail=true';
        $items      = '<a href="'.$url.'">Click here</a>';
        $info       = [
            'module'    => $module,
            'title'     => $title,
            'details'   => [
                'RPF #'             => $data['id'],
                'Status'            => ucwords(str_replace('_', ' ', $data['status'])),
                'Delivery Date'     => format_date($data['date_needed']),
                'Requested By'      => $data['created_by'],
                'Requested At'      => $data['created_at'],
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