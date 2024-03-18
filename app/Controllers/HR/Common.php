<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Traits\HRTrait;

class Common extends BaseController
{
    /* Declare trait here to use */
    use HRTrait;

    /* Search employees */
    public function searchEmployees()
    {
        try {
            $options    = $this->request->getVar('options') ?? [];
            $result     = $this->fetchEmployees(
                $this->request->getVar('q'),
                $options
            );

            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            $this->logExceptionError($e, __METHOD__);
        }
    }
}
