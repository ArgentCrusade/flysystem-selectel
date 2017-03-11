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
use ArgentCrusade\Flysystem\SelectelAdapter;
use ArgentCrusade\Selectel\CloudStorage\Api\ApiClient;
use ArgentCrusade\Selectel\CloudStorage\CloudStorage;
use League\Flysystem\Filesystem;

$api = new ApiClient('selectel-username', 'selectel-password');
$storage = new CloudStorage($api);
$container = $storage->getContainer('container-name');

$adapter = new SelectelAdapter($container);
$filesystem = new Filesystem($adapter);
```

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
