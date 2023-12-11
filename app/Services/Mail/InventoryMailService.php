<?php

namespace App\Services\Mail;

class InventoryMailService extends BaseService
{
    /**
     * Send the PRF mail notification
     *
     * @param   array $data
     * @return  void
     */
    public function sendPrfMailNotif($data, $module_code = null)
    {
        $module_code = $module_code ? $module_code : get_module_codes('inventory_prf');
        if (! $this->isMailNotifEnabled($module_code)) return;

        $module     = 'Project Request Form';
        $title      = $data['status'] === 'pending' ? $module .' Created' : $module .' Accepted';
        $url        = url_to('prf.home') . '?id='. $data['id'] .'&mail=true';
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
        $this->sendMail($info, $title);
    }
}