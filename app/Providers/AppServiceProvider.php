<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use \View;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('system.include.menu', 'App\ViewComposers\ContentObjectTypesViewComposer');
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'Illuminate\Contracts\Auth\Registrar',
            'App\Services\Registrar'
        );
        $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
    }

}
