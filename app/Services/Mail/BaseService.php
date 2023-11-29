<?php

namespace App\Services\Mail;

use App\Traits\MailTrait;

class BaseService
{
    /* Declare trait here to use */
    use MailTrait;

    /**
     * Send mail notification
     *
     * @param   array $info     The details of the mail
     * @param   string $title   The subject of the mail
     * @return  void
     */
    public function sendMail($info, $title)
    {
        try {
            $sendTo     = session('email_address');
            $sendName   = session('name');
            $subject    = 'User Notification - ' . $title;
            $body       = $this->mailTemplate($info);
    
            // Send the mail via SMTP
            $mail = $this->sendSMTPMail($sendTo, $sendName, $subject, $body);
            $this->logInfo($mail, $title, $info, __METHOD__);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}