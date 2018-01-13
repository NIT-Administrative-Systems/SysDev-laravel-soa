# Northwestern SOA Bindings for Laravel
This package provides simple classes for accessing popular SOA services from Laravel applications.

| Service | Prerequisites |
| --- | --- |
| WebSSO | None |
| DirectorySearch | Data steward approval, Apigee API key |
| Generic MQ Publisher | Service account, queues, and Apigee API key |
| Generic MQ Consumer | Service account, queues, and Apigee API key |

## Installation
You can install the package via composer:

```bash
composer require northwestern-sysdev/laravel-soa
```

Publish the config file:

```bash
php artisan vendor:publish --provider="Northwestern\SysDev\SOA\Providers\NuSoaServiceProvider"
```

And finally, add the options to your `.env` file (and don't forget to update `.env.example` for the rest of your team!):

```bash
# DirectorySearch
DIRECTORY_SEARCH_API_KEY=

# MQ Consumer & Publisher
MQ_API_URL=
MQ_API_KEY=
MQ_API_USERNAME=
MQ_API_PASSWORD=
```

## Usage
todo!
