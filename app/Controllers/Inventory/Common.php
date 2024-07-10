<?php

namespace App\Controllers\Inventory;

use App\Controllers\BaseController;
use App\Traits\InventoryTrait;

class Common extends BaseController
{
    /* Declare trait here to use */
    use InventoryTrait;

    /* Search Job Order by quotation number */
    public function searchJobOrders()
    {
        try {
            $options = $this->request->getVar('options') ?? [];
            $results = $this->fetchJobOrders(
                $this->request->getVar('q'),
                $options
            );

            return $this->response->setJSON($results);
        } catch (\Exception $e) {
            $this->logExceptionError($e, __METHOD__);
        }
    }

    /* Search inventory masterlist by model & description */
    public function searchMasterlist()
    {
        try {
            $options = $this->request->getVar('options') ?? [];
            $results = $this->fetchMatestlist(
                $this->request->getVar('q'),
                $options
            );

            return $this->response->setJSON($results);
        } catch (\Exception $e) {
            $this->logExceptionError($e, __METHOD__);
        }
    }

    /* Search order forms by id, client name or client branch name */
    public function searchOrderForms()
    {
        try {
            $options = $this->request->getVar('options') ?? [];
            $results = $this->fetchOrderForms(
                $this->request->getVar('q'),
                $options
            );

            return $this->response->setJSON($results);
        } catch (\Exception $e) {
            $this->logExceptionError($e, __METHOD__);
        }
    }
}
