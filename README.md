<p align="center"><img src="https://user-images.githubusercontent.com/1995501/158461714-0f9fd149-6ad3-4485-9536-e2a82f6aa0e6.png" width="200"></p>

# Suspicion

![GitHub](https://img.shields.io/github/license/livingstoneco/suspicion?label=License)
![GitHub Actions](https://github.com/livingstoneco/suspicion/actions/workflows/main.yml/badge.svg)

An opinionated Laravel package designed to prevent common types of form spam.

## Installation

1. Install package via composer:

```bash
composer require livingstoneco/suspicion
```

2. Run migrations:

```bash
php artisan migrate
```

3. Append `IsRequestSuspicious` middleware to `$routeMiddleware` array in `app/Http/kernel.php`

```php
'isSuspicious' => \Livingstoneco\Suspicion\Http\Middleware\IsRequestSuspicious::class
```

4. Assign `isSuspicious` middleware to routes that accept form input

```php
Route::post('/contact', 'ContactController@send')->middleware('isSuspicious');
```

5. Publish views

```php
php artisan vendor:publish --provider="Livingstoneco\Suspicion\SuspicionServiceProvider" --tag="views"
```

6. Publish configuration

```php
php artisan vendor:publish --provider="Livingstoneco\Suspicion\SuspicionServiceProvider" --tag="config"
```

## Block Repeat Offenders (optional)

Suspicion includes global middleware to block repeat offenders from accessing the entire website.

1. Simply append the `\Livingstoneco\Suspicion\Http\Middleware\IsRepeatOffender::class` middleware to the `$middlewareGroups['web']` array in `app/Http/kernel.php`

```php
\Livingstoneco\Suspicion\Http\Middleware\IsRepeatOffender::class
```

2. The threshold used to determine repeat offenders, http status code and error message returned can be customized using the `repeat_offenders` array in `config/suspicion.php`

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
