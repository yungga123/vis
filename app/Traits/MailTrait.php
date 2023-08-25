<?php

namespace App\Traits;

use App\Models\MailConfigModel;
use App\Libraries\Mail\PHPMailerSMTPService;

trait MailTrait
{
    /**
     * Sending mail via SMPT
     *
     * @param string|array $to
     * @param string $subject
     * @param string $body
     * @param array $cc
     * @param array $attach
     * @return string
     */
    public function sendSMTPMail(
        $to, 
        $toName, 
        $subject, 
        $body, 
        $cc = [], 
        $attach = []
    )
    {
        $mailService    = new PHPMailerSMTPService();
        $mailModel      = new MailConfigModel();
        $mailConfig     = $mailModel->getMailConfig();
        $message        = 'Mail could not be sent! ';

        try {
            if (empty($mailConfig)) {
                throw new \Exception($message .'There is no mail config data.', 1);
            }
            
            if ($mailConfig['is_enable'] === 'NO') {
                throw new \Exception($message .'Mail sending has been <strong>disabled</strong>.', 1);
            }

            $cc += explode(',', $mailConfig['recepients']);
            $mailService->authenticate($mailConfig['email'], $mailConfig['password']);
            $mailService
                ->from($mailConfig['email'], $mailConfig['email_name'])
                ->to($to, $toName)->cc($cc)
                ->subject($subject)->body($body)
                ->attach($attach)
                ->send();
            
            $message = 'Mail has been sent.';
        } catch (\Exception $e) {
            $message = 'Mail could not be sent! Please contact your system administrator.';
            log_message(
                'error',
                "Mail could not be sent. \n Mailer Error: {mail_error}! \n[ERROR] {exception}! \nError code: {code}",
                ['mail_error' => $mailService->ErrorInfo, 'exception' => $e, 'code' => $e->getCode()]
            );

            if ($e->getCode() == 1) $message = $e->getMessage();
        }

        return ' '. $message;
    }
}