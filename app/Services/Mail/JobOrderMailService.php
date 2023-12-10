<?php

namespace App\Services\Mail;

class JobOrderMailService extends BaseMailService implements MailServiceInterface
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
        $module_code = $module_code ? $module_code : get_module_codes('job_orders');
        if (! $this->isMailNotifEnabled($module_code)) return;

        $module     = 'Job Order';
        $title      = $data['status'] === 'pending' ? $module .' Created' : $module .' Accepted';
        $info       = [
            'module'    => $module,
            'title'     => $title,
            'details'   => [
                'Job Order #'       => $data['id'],
                'Tasklead #'        => $data['tasklead_id'] ? $data['tasklead_id'] : 'N/A',
                'Manual Quotation?' => $data['is_manual'],
                'Quotation #'       => $data['quotation'],
                'Quotation Type'    => empty($data['tasklead_type']) ? 'Project' : ucwords($data['tasklead_type']),
                'Client'            => $data['client'],
                'Manager'           => empty($data['manager']) ? 'N/A' : $data['manager'],
                'Status'            => ucwords(str_replace('_', ' ', $data['status'])),
                'Date Requested'    => $data['date_requested'],
                'Date Committed'    => empty($data['date_committed']) ? 'N/A' : $data['date_committed'],
                'Requested By'      => $data['requested_by'],
                'Created At'        => $data['created_at'],
            ],
        ];

        // Send the mail
        $this->sendMail($info, $title, $module_code);
    }
}