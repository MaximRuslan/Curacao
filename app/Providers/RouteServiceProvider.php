<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));

        Route::prefix('admin')
            ->as('admin1.')
            ->middleware(['web'])
            ->namespace($this->namespace . '\\Admin1')
            ->group(base_path('routes/admin1.php'));

        Route::prefix('client')
            ->as('client1.')
            ->middleware(['web'])
            ->namespace($this->namespace . '\\Client1')
            ->group(base_path('routes/client1.php'));

        Route::prefix('merchant')
            ->as('merchant.')
            ->middleware(['web'])
            ->namespace($this->namespace . '\\Merchant')
            ->group(base_path('routes/merchant.php'));

    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace . '\\Api\V1')
            ->group(base_path('routes/api.php'));
    }
}
