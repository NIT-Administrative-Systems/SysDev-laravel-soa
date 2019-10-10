# Northwestern Tools for Laravel
This package enhanced Laravel with easy access to popular Northwestern APIs & webSSO/Duo multi-factor authentication.

| Service          | Prerequisites                         |
| ---------------- | ------------------------------------- |
| WebSSO           | None, optional Duo integration keys   |
| Directory Search | Data steward approval, Apigee API key |
| EventHub         | Topics or queues, Apigee API key      |

## Installation
You can install the package via composer:

```bash
composer require northwestern-sysdev/laravel-soa
php artisan vendor:publish --provider="Northwestern\SysDev\SOA\Providers\NuSoaServiceProvider"
```

And finally, add the options to your `.env` file (and don't forget to update `.env.example` for the rest of your team!) for the services you want to use:

```bash
# DirectorySearch
DIRECTORY_SEARCH_API_KEY=

# EventHub
EVENT_HUB_BASE_URL=https://northwestern-dev.apigee.net
EVENT_HUB_API_KEY=
EVENT_HUB_EVENT_HUB_HMAC_VERIFICATION_SHARED_SECRET=
```

## Usage
The API objects should be injected by the Laravel service container. This ensures the configuration is injected into the objects for you:

```php
<?php

namespace App\Http\Controllers;

use Northwestern\SysDev\SOA\WebSSO;

class MyController extends Controllers
{
    protected $sso;

    public function __construct(WebSSO $sso)
    {
        $this->sso = $sso;
    }

}
```

If you are ever in a spot where injection is unavailable, you can always call `resolve` yourself. This is particularly handy in the tinker console:

```php
$pub = resolve(Northwestern\SysDev\SOA\WebSSO::class);
```

For troubleshooting, each API has a `getLastError()` method. `dd()`ing the API object should give you everything you'll need, including the request body and URL.