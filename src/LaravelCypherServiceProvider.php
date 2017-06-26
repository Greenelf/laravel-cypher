<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 24.06.17
 * Time: 18:54
 */
namespace Greenelf\LaravelCypher;

use Illuminate\Support\ServiceProvider;

class LaravelCypherServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/publish/config/cypher.php' => config_path('cypher.php'),
        ]);
    }

    public function register()
    {
        $this->app->bind('laravelcypher', function () {
            return new LaravelCypher;
        });
    }
}