<?php

namespace App\Services\Mail;

use App\Models\MailNotifModel;
use App\Traits\MailTrait;

class BaseMailService
{
    /* Declare trait here to use */
    use MailTrait;

    /**
     * Send mail notification
     *
     * @param   array $info     The details of the mail
     * @param   string $title   The subject of the mail
     * @param   string|null $module_code
     * @param   array|null $options To pass an email_address and name as new options
     * - format: 
     *          [
     *              'send_to'   => 'john@doe.com'
     *              'send_name' => 'John Doe'
     *          ]
     * 
     * @return  void
     */
    public function sendMail($info, $title, $module_code = null, $options = null)
    {
        try {
            $model      = new MailNotifModel();
            $sendTo     = ($options && isset($options['send_to'])) 
                ? $options['send_to'] : session('email_address');
            $sendName   = ($options && isset($options['send_name'])) 
                ? $options['send_name'] : session('name');
            $subject    = 'User Notification - ' . $title;
            $body       = $this->mailTemplate($info);
            $cc         = $module_code ? clean_param($model->getCCRecipients($module_code, true)) : [];
    
            // Send the mail via SMTP
            $mail = $this->sendSMTPMail($sendTo, $sendName, $subject, $body, $cc);
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