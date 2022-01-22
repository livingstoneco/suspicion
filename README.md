# Suspicion

![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/livingstoneco/suspicion)
![Packagist Downloads](https://img.shields.io/packagist/dt/livingstoneco/suspicion?label=Downloads)
![GitHub](https://img.shields.io/github/license/livingstoneco/suspicion?label=License)
![GitHub Actions](https://github.com/livingstoneco/suspicion/actions/workflows/main.yml/badge.svg)

Prevent common types of form spam in Laravel applications

## Installation

Install package via composer:

```bash
composer require livingstoneco/suspicion
```

Publish configuration

```bash
php artisan vendor:publish --provider="Livingstoneco\Suspicion\SuspicionServiceProvider" --tag="config"
```

Run migrations:

```bash
php artisan migrate
```

## Usage

@TODO


### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email neil@livingstone.co instead of using the issue tracker.

## Credits

-   [Neil Livingstone](https://github.com/nlivingstone)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

