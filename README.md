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

### Block Repeat Offenders (optional)

Suspicion includes gloabl middleware to block repeat offenders from accessing the entire website.

1. Simply append the `\Livingstoneco\Suspicion\Http\Middleware\IsRepeatOffender::class` middleware to the `$middlewareGroups['web']` array in `app/Http/kernel.php`

```php
'IsRepeatOffender' => \Livingstoneco\Suspicion\Http\Middleware\IsRepeatOffender::class
```

2. The threshold used to determine repeat offenders, http status code and error message returned can be customized using the `repeat_offenders` array in `config/suspicion.php`

```php
    'repeat_offenders' => [
        'threshold' => 5,
        'http_code' => 403,
        'message' => 'We are unable to process your request due to suspicious traffic from your network. If your request is urgent, please contact us by phone.'
    ]
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for an outline of what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email neil@livingstone.co instead of using the issue tracker.

## Credits

- [Neil Livingstone](https://github.com/nlivingstone)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
