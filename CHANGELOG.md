# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added


### Changed
- Support for PHP 7.4 and 8.0 has been dropped.

### Fixes
- `NorthwesternAzureProvider::getAccessToken()` was not working. This has been corrected.
- A number of issues with incorrect types have been corrected.
- The `eventhub:queue:status` artisan command was broken on current versions of Laravel. This has been corrected. 

## [v9.1.0] - 2023-10-12

### Added
- Added a config parameter for Azure AD/Entra ID `domain_hint`, for better multi-tenant app registration support.