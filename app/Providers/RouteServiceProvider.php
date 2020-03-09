<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

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

        Route::bind('siswa', function($siswa){
            return \App\Siswa::findOrFail($siswa);

            //atau seperti dibawah ini juga bisa
           // \Route::model('siswa', \App\Siswa::class);
        });

        Route::bind('kelas', function($kelas){
            return \App\Kelas::findOrFail($kelas);

            //atau seperti dibawah ini juga bisa
           // \Route::model('kelas', \App\Kelas::class);
            //$router->model('kelas', \App\Kelas::class); gini bisa juga
        });

        Route::bind('hobi', function($hobi){
            return \App\Hobi::findOrFail($hobi);

            //atau seperti dibawah ini juga bisa
           // \Route::model('kelas', \App\Kelas::class);
            //$router->model('kelas', \App\Kelas::class); gini bisa juga
        });
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

        //
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
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
