<?php

namespace Webkul\WeAccept\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use View;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
   
        Event::listen('sales.order.page_action.before', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('weaccept::admin.sales.orders.view');
        });
    }
}
