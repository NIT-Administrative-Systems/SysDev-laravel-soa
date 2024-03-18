# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased
## [v11.0.0] - 2024-03-18
### Changed
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

## [v9.1.0] - 2023-10-12

### Added
- Added a config parameter for Azure AD/Entra ID `domain_hint`, for better multi-tenant app registration support.