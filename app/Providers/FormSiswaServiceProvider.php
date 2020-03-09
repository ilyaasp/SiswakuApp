<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Kelas;
use App\Hobi;

class FormSiswaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('siswa.form', function($view) {
            $view->with('list_kelas', Kelas::all()->pluck('nama_kelas', 'id'));
            $view->with('list_hobi', Hobi::all()->pluck('nama_hobi', 'id'));
        });

        view()->composer('siswa.form_pencarian', function($view){
            $view->with('list_kelas', Kelas::all()->pluck('nama_kelas', 'id'));
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    
}
