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

## Upgrade

### From 1.0* to 1.1.0
New setting `container_url` was added. You can set your container's custom domain here (for example, `https://static.example.org`) and this option will be used when retrieving fully qualified URLs to files and directories.

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
    'username' => 'selectel-username',
    'password' => 'selectel-password',
    'container' => 'selectel-container',
    'container_url' => 'https://static.example.org',
]
```

`container_url` setting (new in version **1.1.0**) allows you to override default Selectel's CDN domain (if you have custom domain attached). You may omit this setting if you're using default domain, file URLs will look like `http://XXX.selcdn.ru/container_name/path/to/file.txt`, where `XXX` - your unique subdomain (`X-Storage-Url` header value).


Add `ArgentCrusade\Flysystem\Selectel\SelectelServiceProvider::class` to your providers list in `config/app.php`


```php
/*
 * Package Service Providers...
 */
ArgentCrusade\Flysystem\Selectel\SelectelServiceProvider::class,
```


Now you can use Selectel disk as


```php
use Illuminate\Support\Facades\Storage;

Storage::disk('selectel')->put('file.txt', 'Hello world');
```


Also you may want to set `selectel` as default disk to ommit `disk('selectel')` calls and use storage just as `Storage::put('file.txt', 'Hello world')`.


For more info please refer to Laravel's [Storage System](https://laravel.com/docs/5.4/filesystem) documentation.

## Unsupported methods

Due to the implementation of the Selectel API some methods are missing or may not function as expected.

### Visibility management

Selectel provides visibility support only for Containers, but not for files. The change of visibility for the entire container instead of a single file/directory may be confusing for adapter users. Adapter will throw `LogicException` on `getVisibility`/`setVisbility` calls.

### Directories management

Currently Selectel Adapter can display and delete only those directories that were created via `createDir` method. Dynamic directories (those that were created via `write`/`writeStream` methods) can not be deleted or listed as directory.


```php
$fs = new Filesystem($adapter);

$fs->createDir('images'); // The 'images' directory can be deleted and will be listed as 'dir' in the results of `$fs->listContents()`.

$fs->write('documents/hello.txt'); // The 'documents' directory can not be deleted and won't be listed in the results of `$fs->listContents()`.
```


## Note on Closing Streams

Selectel Adapter leaves the streams **open** after consuming them. Make sure that you've closed all streams that you opened.


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
