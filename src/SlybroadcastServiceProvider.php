<?php

namespace Riazxrazor\Slybroadcast;


use Illuminate\Support\ServiceProvider;

class SlybroadcastServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $dist = __DIR__.'/../config/slybroadcast.php';
        $this->publishes([
            $dist => config_path('slybroadcast.php'),
        ],'config');

        $this->mergeConfigFrom($dist, 'slybroadcast');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton(Slybroadcast::class, function($app){
            $config = $app['config']->get('slybroadcast');

            if(!$config){
                throw new \RuntimeException('missing slybroadcast configuration section');
            }

            if(!isset($config['USER_EMAIL'])){
                throw new \RuntimeException('missing slybroadcast configuration: `USER_EMAIL`');
            }

            if(!isset($config['PASSWORD'])){
                throw new \RuntimeException('missing slybroadcast configuration: `PASSWORD`');
            }

            return new Slybroadcast($config['USER_EMAIL'],$config['PASSWORD']);
        });

        $this->app->alias(Slybroadcast::class, 'slybroadcast-api');
    }

}