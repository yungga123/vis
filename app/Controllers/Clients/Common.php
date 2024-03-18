<?php

namespace App\Controllers\Clients;

use App\Controllers\BaseController;
use App\Traits\ClientTrait;

class Common extends BaseController
{
    /* Declare trait here to use */
    use ClientTrait;

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
            $this->logExceptionError($e, __METHOD__);
        }
    }

    /* Search customer branches by id or branch_name */
    public function searchCustomerBranches()
    {
        try {
            $options    = $this->request->getVar('options') ?? [];
            $result     = $this->fetchCustomerBranches(
                $this->request->getVar('q'),
                $options
            );
            
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            $this->logExceptionError($e, __METHOD__);
        }
    }
}
