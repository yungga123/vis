<?php

namespace App\Services\Mail;

class PurchasingMailService extends BaseService
{
    /**
     * Send the RPF mail notification
     *
     * @param   array $data
     * @return  void
     */
    public function sendRpfMailNotif($data, $module_code = null)
    {
        $module_code = $module_code ? $module_code : get_module_codes('task_lead');
        if (! $this->isMailNotifEnabled($module_code)) return;

        $module     = 'Request to Purchase Form';
        $title      = $data['status'] === 'pending' ? $module .' Created' : $module .' Accepted';
        $url        = url_to('rpf.home') . '?id='. $data['id'] .'&mail=true';
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
        $this->sendMail($info, $title);
    }
}