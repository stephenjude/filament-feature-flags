# Filament Feature Flags

Simple feature flags and segments with Filament Admin Panel and Laravel Pennant.

- Apply features for a **segment** of users. Example by country or currency.
- Apply features for **individual** users. Example by email or ID.
- Apply features for **all** users.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stephenjude/filament-feature-flags.svg?style=flat-square)](https://packagist.org/packages/stephenjude/filament-feature-flags)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/stephenjude/filament-feature-flags/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/stephenjude/filament-feature-flags/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/stephenjude/filament-feature-flags/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/stephenjude/filament-feature-flags/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/stephenjude/filament-feature-flags.svg?style=flat-square)](https://packagist.org/packages/stephenjude/filament-feature-flags)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require stephenjude/filament-feature-flags
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-feature-flags-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-feature-flags-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-feature-flags-views"
```

## Usage

```php
$featurePlugin = new Stephenjude\FeaturePlugin();
echo $featurePlugin->echoPhrase('Hello, Stephenjude!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [stephenjude](https://github.com/stephenjude)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
