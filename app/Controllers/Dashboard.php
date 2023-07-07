<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    /**
     * Display the index view
     *
     * @return view
     */
    public function index()
    {
        $data['title']          = 'Dashboard';
        $data['page_title']     = 'Dashboard';
        $data['exclude_toastr'] = true;
        $data['modules']        = $this->moduleBoxMenu();

        return view('dashboard/dashboard', $data);
    }

    /**
     * Get the module card menu
     *
     * @return string (html)
     */
    public function moduleBoxMenu()
    {
        $counter    = 0;
        $html       = '';
        $modules    = $this->modules;
        $bgColor    = [
            'bg-info', 
            'bg-primary', 
            'bg-warning', 
            'bg-danger', 
            'bg-secondary', 
        ];

        $hr_html        = '';
        $sales_html     = '';
        $clients_html   = '';
        $settings_html  = '';
        $inventory_html = '';
        $client_modules = ['CUSTOMERS_COMMERCIAL', 'CUSTOMERS_RESIDENTIAL'];

        if (! empty($modules) && is_array($modules)) {
            // Sort modules ascending
            sort($modules);

            $setup_modules = array_keys(setup_modules());

            foreach ($modules as $val) {
                // Not include DASHBOARD module
                
                if ($val !== 'DASHBOARD' && in_array($val, $setup_modules)) {
                    $module = setup_modules($val);

                    // Add module card menu
                    $card = <<<EOF
                        <div class="small-box bg-success">
                            <div class="inner"><h4>{$module['name']}</h4></div>
                            <div class="icon"><i class="{$module['icon']}"></i></div>
                            <a href="{$module['url']}" class="small-box-footer">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    EOF;

                    switch ($module['menu']) {
                        case 'HUMAN_RESOURCE':
                            $hr_html .= $card;
                            break;
                        case 'SALES':
                            if (in_array($val, $client_modules)) {
                                $clients_html .= $card;
                            } else {
                                $sales_html .= $card;
                            }
                            break;
                        case 'SETTINGS':
                            $settings_html .= $card;
                        default:
                            if ($val === 'INVENTORY') $inventory_html .= $card;
                        break;
                    }

                    $counter++;
                }
            }
        } else {
            $html = '<h2>No module card to be displayed!</h2>';
        }

        return (! empty($html) && $counter === 0) ? $html : [
            'hr_modules'        => $this->cardHtml($hr_html, 'Human Resource', 'info'),
            'clients_modules'   => $this->cardHtml($clients_html, 'Clients', 'primary'),
            'sales_modules'     => $this->cardHtml($sales_html, 'Sales', 'success'),
            'inventory_modules' => $this->cardHtml($inventory_html, 'Inventory', 'secondary'),
            'settings_modules'  => $this->cardHtml($settings_html, 'Settings', 'danger'),
        ];
    }

    private function cardHtml($html, $title, $type)
    {
        if (! empty($html)) {
            $html = <<<EOF
                <div class="col-4">
                    <div class="card card-{$type} card-outline">
                        <div class="card-header">
                            <h5 class="card-title">{$title}</h5>
                        </div>
                        <div class="card-body">
                            $html
                        </div>
                    </div>
                </div>	
            EOF;
        }

        return $html;
    }
}