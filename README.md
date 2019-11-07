# Northwestern SOA Bindings for Laravel [![Build Status](https://travis-ci.org/NIT-Administrative-Systems/SysDev-laravel-soa.svg?branch=master)](https://travis-ci.org/NIT-Administrative-Systems/SysDev-laravel-soa) [![Latest Stable Version](https://poser.pugx.org/northwestern-sysdev/laravel-soa/v/stable)](https://packagist.org/packages/northwestern-sysdev/laravel-soa) [![Total Downloads](https://poser.pugx.org/northwestern-sysdev/laravel-soa/downloads)](https://packagist.org/packages/northwestern-sysdev/laravel-soa) [![Coverage Status](https://coveralls.io/repos/github/NIT-Administrative-Systems/SysDev-laravel-soa/badge.svg?branch=master)](https://coveralls.io/github/NIT-Administrative-Systems/SysDev-laravel-soa?branch=master)
This package enhanced Laravel with easy access to popular Northwestern APIs & webSSO/Duo multi-factor authentication.

## Installation & Usage
For installation instructions and more, please check out the documentation at [https://nit-administrative-systems.github.io/SysDev-laravel-soa/](https://nit-administrative-systems.github.io/SysDev-laravel-soa/).

## Contributing
If you'd like to contribute to the library, you are welcome to submit a pull request!

My ideal architecture (going forward :cold_sweat:) is to write plain-old PHP bindings (so folks can use them in non-Laravel apps) and then have the `NuSoaServiceProvider` inject config & add any other Laravel-specific enhancements.

If there is a service on the API Registry that you'd like to see included, please go ahead and open an issue requesting it.
