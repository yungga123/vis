<?php

namespace App\Controllers\Purchasing;

use App\Controllers\BaseController;
use App\Traits\PurchasingTrait;

class Common extends BaseController
{
    /* Declare trait here to use */
    use PurchasingTrait;

    /* Search Suppliers by name */
    public function searchSuppliers()
    {
        try {
            $options = $this->request->getVar('options') ?? [];
            $results = $this->fetchSuppliers(
                $this->request->getVar('q'),
                $options
            );

            return $this->response->setJSON($results);
        } catch (\Exception $e) {
            $this->logExceptionError($e, __METHOD__);
        }
    }

    /* Search rpf by id */
    public function searchRpf()
    {
        try {
            $options = $this->request->getVar('options') ?? [];
            $results = $this->fetchRpf(
                $this->request->getVar('q'),
                $options
            );

            return $this->response->setJSON($results);
        } catch (\Exception $e) {
            $this->logExceptionError($e, __METHOD__);
        }
    }
}
