<?php

namespace App\Services\Mail;

class EmployeeMailService extends BaseMailService implements MailServiceInterface
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
        $module_code = $module_code ? $module_code : get_module_codes('employees');
        if (! $this->isMailNotifEnabled($module_code)) return;

        $module     = 'Employee Record';
        $title      = $module .' Created';
        $info       = [
            'module'    => $module,
            'title'     => $title,
            'details'   => [
                'Employee ID'       => $data['employee_id'],
                'Employee Name'     => $data['employee_name'],
                'Gender'            => $data['gender'],
                'Civil Status'      => ucwords($data['civil_status']),
                'Position'          => $data['position'],
                'Date Hired'        => format_date($data['date_hired']),
                'Employment Status' => $data['employment_status'],
                'Email Address'     => $data['email_address'],
                'Contact Number'    => $data['contact_number'],
                'Created By'        => $data['created_by'],
                'Created At'        => format_datetime($data['created_at']),
            ],
        ];

        // Send the mail
        $this->sendMail($info, $title, $module_code);
    }
}