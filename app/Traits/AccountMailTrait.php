<?php

namespace App\Traits;

use App\Models\EmployeesModel;

trait AccountMailTrait
{
    use MailTrait;

    /**
     * Sending mail account notification
     *
     * @param string $employeeId
     * @param array $request
     * @param bool $change
     * @return string
     */
    public function sendMailAccountNotif($employeeId, $request, $change = false)
    {
        $empModel   = new EmployeesModel();
        // Get employe name and email address
        $employee   = $empModel->getEmployeeDetails($employeeId, 'employee_name, email_address');
        $sendTo     = $employee['email_address'];
        $sendName   = $employee['employee_name'];
        // Set mail subject
        $subject    = $change ? 'Password changed confirmation!' : 'Account confirmation!';
        // Format the mail body
        $body       = $this->mailAccountBodyFormat($sendName, $request, $change);

        // Send the mail via SMTP
        return $this->sendSMTPMail($sendTo, $sendName, $subject, $body);
    }

    /**
     * The body template/format for account sending mail
     *
     * @param string $name      Employee account name
     * @param string $request   Contains employee account username & raw password
     * @param bool $change      Identifier if password change or not
     * @return string
     */
    protected function mailAccountBodyFormat($name, $request, $change = false)
    {
        $name       = ucfirst($name);
        $username   = $request['username'];
        $password   = $request['password'];
        $body       = "<p>Hi {$name},</p>";

        if ($change) {
            $body .= "
                <p>
                    Your password has been changed. You can now login using the credentials below:
                </p>
            ";
        } else {
            $body .= "
                <p>
                    Your account has been created for <b>Vinculum MIS</b>! You can now login using the credentials below:
                </p>
            ";
        }

        $login = site_url('/login');
        $body .= "
            Username: {$username} <br>
            Password: {$password} <br>
            Link: {$login} <br>

            <p>
                Regards, <br>
                Vinculum MIS<br>
                <small><i>[This is auto generated. Please don't reply to this email!]</i></small>
            </p>
        ";

        return $body;
    }
}