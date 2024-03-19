# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased
## [v9.1.1] - 2024-02-19
This is a backport release to support apps still using PHP 8.1.

### Fixes
- Fixed a bug when configuring EventHub webhooks using `php artisan eventhub:webhook:configure`.

## [v9.1.0] - 2023-10-12

### Added
- Added a config parameter for Azure AD/Entra ID `domain_hint`, for better multi-tenant app registration support.