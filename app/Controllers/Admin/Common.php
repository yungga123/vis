<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Traits\AdminTrait;

class Common extends BaseController
{
    /* Declare trait here to use */
    use AdminTrait;

    /* Search task lead booked by quotation */
    public function searchQuotation()
    {
        try {
            $options = $this->request->getVar('options') ?? [];
            $result = $this->findBookedTaskLeadsByQuotation(
                $this->request->getVar('q'),
                $options
            );

            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
        }
    }

    /* Search schedules by id, title or description */
    public function searchSchedules()
    {
        try {
            $options    = $this->request->getVar('options') ?? [];
            $result     = $this->fetchSchedules(
                $this->request->getVar('q'),
                $options
            );
    
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
        }
    }

    /* Search customers by customer_name */
    public function searchCustomers()
    {
        try {
            $options    = $this->request->getVar('options') ?? [];
            $result     = $this->fetchCustomers(
                $this->request->getVar('q'),
                $options
            );
            
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
        }
    }
}
