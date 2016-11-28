# Laravel Health Panel And Notifier

[![Latest Stable Version](https://img.shields.io/packagist/v/pragmarx/health.svg?style=flat-square)](https://packagist.org/packages/pragmarx/health) [![License](https://img.shields.io/badge/license-BSD_3_Clause-brightgreen.svg?style=flat-square)](LICENSE) [![Downloads](https://img.shields.io/packagist/dt/pragmarx/health.svg?style=flat-square)](https://packagist.org/packages/pragmarx/health)

This package creates a service status panel for you any Laravel app and has the follwing main points:
 
- Highly extendable and configurable: you can create new checkers and notifiers very easily, and you can virtually change everything on it.
- Resilient: if the framework is working and at least one notification channel, you should receive notification messages. 
- Built in notification system: get notifications via mail, slack, telegram or anything else you need.
- Routes for: panel, json result, string result and resource.
- Configurable panel design.
- View app error messages right in the panel.

## Screenshots 

### Panel

[default panel](docs/error-multi.png)

## Requirements

- PHP 5.6+

## Compatibility

You don't need Laravel to use it, but it's compatible with

- Laravel 5.2+

## Installing

Use Composer to install it:

```
composer require pragmarx/health
```

## Installing on Laravel

Add the Service Provider and Facade alias to your `app/config/app.php` (Laravel 4.x) or `config/app.php` (Laravel 5.x):

    PragmaRX\Health\ServiceProvider::class,

    'Health' => PragmaRX\Health\Vendor\Laravel\Facade::class,

## Author

[Antonio Carlos Ribeiro](http://twitter.com/iantonioribeiro)

## License

Health is licensed under the BSD 3-Clause License - see the `LICENSE` file for details

## Contributing

Pull requests and issues are more than welcome.
