<?php

namespace App\Services\Mail;

use App\Models\MailNotifModel;
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
     * 
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

    /**
     * Send mail notification
     *
     * @param   string $module_code
     * 
     * @return  void|bool
     */
    public function isMailNotifEnabled($module_code = null)
    {
        if (empty($module_code)) return false;

        $model      = new MailNotifModel();
        $is_enabled = $model->isMailNotifEnabled($module_code);
        
        // For logs
        $message    = "Mail notification for this module is enabled!";
        $title      = 'Check Mail Notif for '. get_modules($module_code);
        $details    = ['module_code' => $module_code, 'is_mail_notif_enabled' => $is_enabled];

        if (! $is_enabled) {
            $message = "Mail notification for this module is off or not enabled!";
        }

        $this->logInfo($message, $title, $details, __METHOD__);

        return $is_enabled;
    }
}