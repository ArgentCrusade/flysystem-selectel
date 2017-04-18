<?php

namespace ArgentCrusade\Flysystem\Selectel;

use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use ArgentCrusade\Selectel\CloudStorage\CloudStorage;
use ArgentCrusade\Selectel\CloudStorage\Api\ApiClient;

class SelectelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Storage::extend('selectel', function ($app, $config) {
            $api = new ApiClient($config['username'], $config['password']);
            $api->authenticate();

            $storage = new CloudStorage($api);
            $container = $storage->getContainer($config['container']);

            if (isset($config['container_url'])) {
                $container->setUrl($config['container_url']);
            }

            return new Filesystem(new SelectelAdapter($container));
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
