# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## [v12.0.0] - 2026-04-10
### Fixed
- `WebSSOAuthentication` trait now regenerates the session after login and invalidates it on logout to prevent session fixation attacks.

### Changed
- Dropped support for PHP 8.2. The minimum required version is now PHP 8.3.
- Support for Laravel 13 has been added.
- Dropped support for Guzzle 6. The minimum required version is now Guzzle 7.
- Dropped support for `laravel/ui` v2 and v3. The minimum required version is now v4.
- Widened `socialiteproviders/manager` constraint from `~4.8.1` to `^4.8.1`.
- Replaced deprecated `Closure::fromCallable('static::...')` callables with first-class callable syntax for PHP 8.5 compatibility.

## [v11.4.0] - 2026-03-11
### Changed
- Bumped minimum `firebase/jwt` and `lcobucci/jwt` versions used by the Entra ID SSO code. 
- Internal implementation details for the Entra ID token signature validations have been changed. This should not impact library users. 

## [v11.3.0] - 2026-01-21
### Added
- Added `laravel/prompts` as a dependency for improved CLI interactions.
- EventHub webhook commands now prompt for confirmation when run in local environments.
- Added `eventHubWebhookActiveWhen()` route macro for conditional webhook activation. These webhooks are still registered, but start paused when the condition is `false`.

### Changed
- All EventHub Artisan commands have been modernized with improved output formatting using Laravel Prompts.

## [v11.2.0] - 2025-02-24
### Added
- Support for Laravel 12 has been added.

## [v11.1.0] - 2024-07-17
### Changed
- For Azure Entra ID SSO, a new `token_verifier` option has been added to facilitate multi-tenant configurations.

## [v11.0.1] - 2024-03-19
### Fixes
- Fixed a bug when configuring EventHub webhooks using `php artisan eventhub:webhook:configure`.

## [v11.0.0] - 2024-03-18
### Changed
- Dropped support for PHP 8.1.
- Support for Laravel 11 has been added

    The route stub has been updated for the new Laravel 11 skeleton. When upgrading, the `withoutMiddleware()` call on the Azure AD callback route must be changed to exclude the `Illuminate\Foundation\Http\Middleware\ValidateCsrfToken` class, since the new Laravel skeleton no longer ships with `App\Http\Middleware\VerifyCsrfToken`.

## [v10.0.0] - 2024-02-06
### Added
- The `eventhub:dlq:restore-messages` artisan command has been added. This is a tool to move messages from the DLQ back to the original queue for re-processing.

### Changed
- Support for PHP 7.4 and 8.0 has been dropped.

### Fixes
- `NorthwesternAzureProvider::getAccessToken()` was not working. This has been corrected.
- A number of issues with incorrect types have been corrected.
- The `eventhub:queue:status` artisan command was broken on current versions of Laravel. This has been corrected. 

## [v9.1.1] - 2024-02-19
This is a backport release to support apps still using PHP 8.1.

### Fixes
- Fixed a bug when configuring EventHub webhooks using `php artisan eventhub:webhook:configure`.

## [v9.1.0] - 2023-10-12

### Added
- Added a config parameter for Azure AD/Entra ID `domain_hint`, for better multi-tenant app registration support.
