<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        view()->composer(
            [
                'admin1.includes.sidebar'
            ], 'App\ViewComposers\SidebarComposer'
        );
        view()->composer(
            [
                'admin1.includes.header'
            ], 'App\ViewComposers\HeaderComposer'
        );
        view()->composer(
            [
                'client1.includes.header'
            ], 'App\ViewComposers\ClientHeaderComposer'
        );
        view()->composer([
            'merchant.includes.header'
        ], 'App\ViewComposers\MerchantHeader');
    }
}
