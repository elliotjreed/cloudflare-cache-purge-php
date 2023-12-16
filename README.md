[![Contributor Covenant](https://img.shields.io/badge/Contributor%20Covenant-v2.0%20adopted-ff69b4.svg)](code-of-conduct.md)

# Cloudflare Cache Purge for PHP

PHP 8.1 or above is required.

This library allows the listing of Cloudflare "Zones" and purging files from the Cloudflare cache within a Zone.

It could be used as part of a deployment process for example.

## Usage

### Cloudflare API Token

To generate a Cloudflare API token visit [dash.cloudflare.com/profile/api-tokens](https://dash.cloudflare.com/profile/api-tokens) and
generate a new API token with "Zone" read and "Cache" purge permissions.

### Listing Zones

```php
<?php

use ElliotJReed\Exception\Cloudflare;
use ElliotJReed\Zones;
use GuzzleHttp\Client;

try {
    $zoneResponse = (new Zones(new Client(), 'SECRET CLOUDFLARE API TOKEN'))
        ->get();

    foreach ($zoneResponse->getResults() as $result) {
        echo 'Zone ID: ' . $result->getId() . \PHP_EOL;
        echo 'Zone name: ' . $result->getName() . \PHP_EOL . \PHP_EOL;
    }
} catch (Cloudflare $exception) {
    echo $exception->getMessage() . \PHP_EOL;
    echo $exception->getPrevious()->getMessage() . \PHP_EOL;
}

```

### Purging files from Cache within a Zone

The Zone ID can be retrieved from either the Cloudlare dashboard (under "API" and "Zone ID" on the right) or by using `ElliotJReed\Zones`.

```php
<?php

use ElliotJReed\Cache;
use ElliotJReed\Exception\Cloudflare;
use GuzzleHttp\Client;

$urls = [
    'https://www.example.com.com/image1.png',
    'https://www.example.com.com/image2.png'
];

try {
    $cacheResponse = (new Cache(new Client(), 'SECRET CLOUDFLARE API TOKEN'))
        ->purgeFiles('zone-id-from-api-or-dashboard', $urls);

    foreach ($cacheResponse->getResults() as $result) {
        echo 'Cache Purge Response ID: ' . $result->getId() . \PHP_EOL . \PHP_EOL;
    }
} catch (Cloudflare $exception) {
    echo $exception->getMessage() . \PHP_EOL;
    echo $exception->getPrevious()->getMessage() . \PHP_EOL;
}

```

## Development

PHP 8.0 or above and Composer is expected to be installed.

### Installing Composer

For instructions on how to install Composer visit [getcomposer.org](https://getcomposer.org/download/).

### Installing

After cloning this repository, change into the newly created directory and run:

```bash
composer install
```

or if you have installed Composer locally in your current directory:

```bash
php composer.phar install
```

This will install all dependencies needed for the project.

Henceforth, the rest of this README will assume `composer` is installed globally (ie. if you are using `composer.phar` you will need to use `composer.phar` instead of `composer` in your terminal / command-line).

## Running the Tests

### Unit tests

Unit testing in this project is via [PHPUnit](https://phpunit.de/).

All unit tests can be run by executing:

```bash
composer phpunit
```

#### Debugging

To have PHPUnit stop and report on the first failing test encountered, run:

```bash
composer phpunit:debug
```

### Static analysis

Static analysis tools can point to potential "weak spots" in your code, and can be useful in identifying unexpected side-effects.

[[Psalm](https://psalm.dev/) is configured at its highest level, meaning false positives are quite likely.

All static analysis tests can be run by executing:

```bash
composer static-analysis
```

## Code formatting

A standard for code style can be important when working in teams, as it means that less time is spent by developers processing what they are reading (as everything will be consistent).

Code formatting is automated via [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer).

PHP-CS-Fixer will not format line lengths which do form part of the PSR-2 coding standards so these will produce
warnings when checked by [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer).

These can be run by executing:

```bash
composer phpcs
```

### Running everything

All of the tests can be run by executing:

```bash
composer test
```

### Outdated dependencies

Checking for outdated Composer dependencies can be performed by executing:

```bash
composer outdated
```

### Validating Composer configuration

Checking that the [composer.json](composer.json) is valid can be performed by executing:

```bash
composer validate --no-check-publish
```

### Running via GNU Make

If GNU [Make](https://www.gnu.org/software/make/) is installed, you can replace the above `composer` command prefixes with `make`.

All of the tests can be run by executing:

```bash
make test
```

#### Github Actions

Look at the example in [.github/workflows/php.yml](.github/workflows/php.yml).

## Built With

  - [PHP](https://secure.php.net/)
  - [Composer](https://getcomposer.org/)
  - [PHPUnit](https://phpunit.de/)
  - [Psalm](https://psalm.dev/)
  - [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)
  - [GNU Make](https://www.gnu.org/software/make/)

## License

This project is licensed under the MIT License - see the [LICENCE.md](LICENCE.md) file for details.
