<?php

namespace App\Services\Mail;

class HRMailService extends BaseService
{
    /**
     * Send the Employee mail notification
     *
     * @param   array $data
     * @return  void
     */
    public function sendEmployeeMailNotif($data)
    {
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
        $this->sendMail($info, $title);
    }
}