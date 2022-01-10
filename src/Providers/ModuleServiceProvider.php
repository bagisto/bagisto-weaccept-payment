<?php

namespace Webkul\WeAccept\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\WeAccept\Models\WeAccept::class,
    
    ];
}
