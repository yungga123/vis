<?php

namespace App\Services\Mail;

use App\Models\AccountModel;

class AccountMailService extends BaseMailService implements MailServiceInterface
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
        $module_code = $module_code ? $module_code : get_module_codes('accounts');
        if (! $this->isMailNotifEnabled($module_code)) return;

        $model      = new AccountModel();
        $columns    = 'employee_name, email_address';
        $record     = $model->getAccountsView($data['employee_id'], null, $columns);

        $module     = 'Account';
        $title      = $module .' '. $data['action'];
        $info       = [
            'module'    => $module,
            'title'     => $title,
            'details'   => [
                'Account Name'  => $record['employee_name'],
                'Username'      => $data['username'],
                'Password'      => $data['password'],
                'Link'          => base_url(),
                $data['action'].' By'    => session('name'),
                $data['action'].' At'    => format_datetime(current_datetime()),
            ],
        ];


        // Send the mail
        $options = [
            'send_to'   => $record['email_address'],
            'send_name' => $record['employee_name'],
        ];
        $this->sendMail($info, $title, $module_code, $options);
    }
}