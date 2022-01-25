# Suspicion

![GitHub](https://img.shields.io/github/license/livingstoneco/suspicion?label=License)
![GitHub Actions](https://github.com/livingstoneco/suspicion/actions/workflows/main.yml/badge.svg)

Prevent common types of form spam in Laravel applications

## Installation

Install package via composer:

```bash
composer require livingstoneco/suspicion
```

Run migrations:

```bash
php artisan migrate
```

## Usage

Append `IsRequestSuspicious` middleware to `$routeMiddleware` array in `app/Http/kernel.php`

```php
'isSuspicious' => \Livingstoneco\Suspicion\Http\Middleware\IsRequestSuspicious::class
```

Assign `isSuspicious` middleware to routes that accept form input

```php
Route::post('/contact', 'ContactController@send')->middleware('isSuspicious');
```

Publish views

```php
php artisan vendor:publish --provider="Livingstoneco\Suspicion\SuspicionServiceProvider" --tag="views"
```

Publish configuration (optional)

```php
php artisan vendor:publish --provider="Livingstoneco\Suspicion\SuspicionServiceProvider" --tag="config"
```

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

