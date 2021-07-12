# Changelog

## 1.0.1 - 2020-10-15
- Check certificates using openSSL

## 1.0.0 - 2020-10-10
- Add SSL certificates checker
- Allow using URL parameter at the same level of method
- Add support for API checks via the HTTP checker and add example
- Add URL to error message when HTTP is not able to connect
- Allow adding HTTP headers to requests
- Allow HTTP checker method to be changed
- Allow HTTP checker to POST with data
- Allow configuration of Guzzle on the HTTP checker

## 0.11.1 - 2020-10-01
## 0.11.0 - 2020-10-01
- Add Laravel 8 support

## 0.10.4 - 2020-10-01
- Fixed the deprecated use of implode
- Add support for phpunit/php-timer 3
- Added $dateFormat for MS SQL Server support
- Fix SensioLabsSecurityChecker results
- Fix Laravel deprecated helpers
- Fix notifier

## 0.10.2 - 2020-08-01
- Fix Laravel 7.22.0 breaking change

## 0.10.0 - 2019-09-11
### Added
- Laravel 6 support 
- Compatibility with PHP 7.4
- Email notification improvements
- Lumen support

## 0.9.16 - 2018-10-08
### Fixed
- Properly use routes from configuration on axios requests. 
  Please check if your route names all starts with 'pragmarx.health.', it's mandatory now

## 0.9.15 - 2018-09-27
### Changed
- Composer outdated now ignores major versions
### Fixed
- Buttons alignment

## 0.9.14 - 2018-09-25
### Added
- Laravel Lumen support

## 0.9.13 - 2018-09-24
### Fixed
- Fix Result status in Security Checker

## 0.9.12 - 2018-09-24
### Fixed
- Fix content types for javascript import

## 0.9.11 - 2018-09-23
### Changed
- Notifications are disabled by default
### Fixed
- Uptime checker
- Use full namespaces for Laravel façades

## 0.9.10 - 2018-09-22
### Added
- Input to filter resources
### Changed
- Flush cache when clicking to refresh one resource in the panel
- Binaries (composer, ping) are now configurable
- Added config key 'services' to configure services binaries
- Checker Ping updated, please review configuration for Latency resources
- Checker Composer updated, please review configuration for PackagesUpToDate resources
### Fix
- Laravel 5.5 support

## 0.9.9 - 2018-09-19
## 0.9.8 - 2018-09-19
## 0.9.7 - 2018-09-17
## 0.9.6 - 2018-09-14
### Fixed
- Some minor bugs

## 0.9.5 - 2018-09-12
### Changed
- Complete refactor
- Can now create many targets inside resources
- Resource charts
- VueJS Panel
- Allow users to click to refresh one item
- Store data on database to plot charts
- Lots of new Checkers and resources

## 0.4.0 - 2017-12-18
### Changed
- User will need change config files and surround expressions ({{ <expression> }}) with "" (breaking change)  
- Upgraded to Laravel 5.5 & PHP 7.0+
- Added support for PHP 7.2

## 0.3.3 - 2017-04-20
### Fixed
- Docusign checker

## 0.3.2 - 2017-04-16
### Fixed
- Yaml functions parser when using strings as parameters

## 0.3.0 - 2017-02-25
### Changed
- Now we can use Yaml files to configure resources

## 0.1.8 - 2017-01-09
### Added
- Broadcasting checker for Redis and Pusher

## 0.1.7 - 2017-01-06
### Added
- Server Uptime checker 
- Server Load checker 

## 0.1.6 - 2017-01-05
### Added
- Queue checker 
- Redis checker

## 0.1.0 - 2016-11-28
### Added
- First usable version 
