<?php

namespace App\Services\Mail;

class PrfMailService extends BaseMailService implements MailServiceInterface
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
        $module_code = $module_code ? $module_code : get_module_codes('inventory_prf');
        if (! $this->isMailNotifEnabled($module_code)) return;

        $module     = 'Project Request Form';
        $title      = $data['status'] === 'pending' ? $module .' Created' : $module .' Accepted';
        $url        = url_to('inventory.prf.home') . '?id='. $data['id'] .'&mail=true';
        $items      = '<a href="'.$url.'">Click here</a>';
        $info       = [
            'module'    => $module,
            'title'     => $title,
            'details'   => [
                'PRF #'             => $data['id'],
                'Job Order #'       => $data['job_order_id'],
                'Status'            => ucwords(str_replace('_', ' ', $data['status'])),
                'Process Date'      => format_date($data['process_date']),
                'Created By'        => $data['created_by'],
                'Created At'        => format_datetime($data['created_at']),
                'Items'             => $items,
            ],
        ];

        // Send the mail
        $this->sendMail($info, $title, $module_code);
    }
}