<?php

namespace App\Services\Mail;

class RpfMailService extends BaseMailService implements MailServiceInterface
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
        $module_code = $module_code ? $module_code : get_module_codes('purchasing_rpf');
        if (! $this->isMailNotifEnabled($module_code)) return;

        $module     = 'Request to Purchase Form';
        $title      = $data['status'] === 'pending' ? $module .' Created' : $module .' Accepted';
        $url        = url_to('purchasing.rpf.home') . '?id='. $data['id'] .'&mail=true';
        $items      = '<a href="'.$url.'">Click here</a>';
        $info       = [
            'module'    => $module,
            'title'     => $title,
            'details'   => [
                'RPF #'             => $data['id'],
                'Status'            => ucwords(str_replace('_', ' ', $data['status'])),
                'Delivery Date'     => format_date($data['date_needed']),
                'Requested By'      => $data['created_by'],
                'Requested At'      => format_datetime($data['created_at']),
                'Items'             => $items,
            ],
        ];

        // Send the mail
        $this->sendMail($info, $title, $module_code);
    }
}