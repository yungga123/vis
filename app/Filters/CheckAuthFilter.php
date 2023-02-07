<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CheckAuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (! $session->get('logged_in')) {
            return redirect()->to('login');
        } else {
            // $is_dashboard = url_is('/') || url_is('dashboard');
            // if (session('access_level') !== AAL_ADMIN && !$is_dashboard) {
            //     $model          = new \App\Models\PermissionModel();
            //     $permissions    = $model->getCurrUserPermissions();
            //     $modules        = array_column($permissions, 'module_code');    
            //     $module_code    = MODULE_CODES_URI[$request->getUri()->getPath()];
            //     // d($modules, $module_code); die;
    
            //     if (! in_array($module_code, $modules)) {
            //         return redirect()->to('access-denied?from='. $request->getPath());
            //     }
            // }    
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
