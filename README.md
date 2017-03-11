# Flysystem Adapter for Selectel Cloud Storage

[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]
[![ScrutinizerCI][ico-scrutinizer]][link-scrutinizer]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)

## Requirements
This package requires PHP 5.6 or higher.

## Installation

You can install the package via composer:

``` bash
$ composer require argentcrusade/flysystem-selectel
```

## Usage

``` php
use ArgentCrusade\Flysystem\Selectel\SelectelAdapter;
use ArgentCrusade\Selectel\CloudStorage\Api\ApiClient;
use ArgentCrusade\Selectel\CloudStorage\CloudStorage;
use League\Flysystem\Filesystem;

$api = new ApiClient('selectel-username', 'selectel-password');
$storage = new CloudStorage($api);
$container = $storage->getContainer('container-name');

$adapter = new SelectelAdapter($container);
$filesystem = new Filesystem($adapter);
```

## Laravel Integration

You can use this adapter with Laravel's [Storage System](https://laravel.com/docs/5.4/filesystem).


Add `selectel` disk to `config/filesystems.php` configuration file (`disks` section):


```php
'selectel' => [
    'driver' => 'selectel',
    'username' => env('SELECTEL_USERNAME'),
    'password' => env('SELECTEL_PASSWORD'),
    'container' => env('SELECTEL_CONTAINER'),
]
```


Add configuration values to your `.env` file:


```
SELECTEL_USERNAME=username
SELECTEL_PASSWORD=password
SELECTEL_CONTAINER=container-name
```


Create new Service Provider via `php artisan make:provider SelectelServiceProvider` command or just create `SelectelServiceProvider.php` file inside of `app/Providers` directory.


```php
<?php

namespace App\Providers;

use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use ArgentCrusade\Flysystem\Selectel\SelectelAdapter;
use ArgentCrusade\Selectel\CloudStorage\CloudStorage;
use ArgentCrusade\Selectel\CloudStorage\Api\ApiClient;

class SelectelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('selectel', function ($app, $config) {
            $api = new ApiClient($config['username'], $config['password']);
            $storage = new CloudStorage($api);
            $container = $storage->getContainer($config['container']);

            return new Filesystem(new SelectelAdapter($container));
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
```


Finally add `App\Providers\SelectelServiceProvider::class` to your providers list in `config/app.php`


```php
/*
 * Application Service Providers...
 */
App\Providers\SelectelServiceProvider::class,
```


Now you can use Selectel disk as


```php
use Illuminate\Support\Facades\Storage;

Storage::disk('selectel')->put('file.txt', 'Hello world');
```


Also you may want to set `selectel` as default disk to ommit `disk('selectel')` calls and use storage just as `Storage::put('file.txt', 'Hello world')`.


For more info please refer to Laravel's [Storage System](https://laravel.com/docs/5.4/filesystem) documentation.


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email zurbaev@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://poser.pugx.org/argentcrusade/flysystem-selectel/version?format=flat
[ico-license]: https://poser.pugx.org/argentcrusade/flysystem-selectel/license?format=flat
[ico-travis]: https://api.travis-ci.org/ArgentCrusade/flysystem-selectel.svg?branch=master
[ico-styleci]: https://styleci.io/repos/84637792/shield?branch=master&style=flat
[ico-scrutinizer]: https://scrutinizer-ci.com/g/ArgentCrusade/flysystem-selectel/badges/quality-score.png?b=master

[link-packagist]: https://packagist.org/packages/argentcrusade/flysystem-selectel
[link-travis]: https://travis-ci.org/ArgentCrusade/flysystem-selectel
[link-styleci]: https://styleci.io/repos/84637792
[link-scrutinizer]: https://scrutinizer-ci.com/g/ArgentCrusade/flysystem-selectel/
[link-author]: https://github.com/tzurbaev
