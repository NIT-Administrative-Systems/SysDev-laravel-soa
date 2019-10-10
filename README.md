# Northwestern SOA Bindings for Laravel [![Build Status](https://travis-ci.org/NIT-Administrative-Systems/SysDev-laravel-soa.svg?branch=master)](https://travis-ci.org/NIT-Administrative-Systems/SysDev-laravel-soa) [![Latest Stable Version](https://poser.pugx.org/northwestern-sysdev/laravel-soa/v/stable)](https://packagist.org/packages/northwestern-sysdev/laravel-soa) [![Total Downloads](https://poser.pugx.org/northwestern-sysdev/laravel-soa/downloads)](https://packagist.org/packages/northwestern-sysdev/laravel-soa)
This package enhanced Laravel with easy access to popular Northwestern APIs & webSSO/Duo multi-factor authentication.

| Service         | Prerequisites                         |
| --------------- | ------------------------------------- |
| WebSSO          | None                                  |
| DirectorySearch | Data steward approval, Apigee API key |
| EventHub        | Queues, Apigee API Key                |

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

## WebSSO
The webSSO API bindings will resolve the value of an `openAMssoToken` cookie into a NetID.

This is merely an API binding; if you want a pre-made workflow for logging in, check out the `northwestern-sysdev/laravel-websso` package.

```php
<?php

namespace App\Http\Controllers;

use Northwestern\SysDev\SOA\WebSSO;

class MyController extends Controllers
{
    public function login(Request $request, WebSSO $sso)
    {
        // Note that $request->cookie() won't work here.
        // It requires that all cookies be set by Laravel & encrypted with the app's key.
        $token = $_COOKIE['openAMssoToken'];

        $netid = $sso->getNetID($token);
        if ($netid == false) {
            // Error
            dd($sso->getLastError());
        }

        dd($netid); // netID as a string with no frills
    }
}
```

## DirectorySearch
The DirectorySearch endpoint requires an Apigee API key, as well as approval from relevant data stewards. For more information requesting access, check out the documents on the [API portal](https://northwestern-apiportal.apigee.io).

Once you get your key, add it to the `.env` file as the `DIRECTORY_SEARCH_API_KEY` property. By default, the production service will be used, but you can define `DIRECTORY_SEARCH_URL` if you want to use dev or QA.

```php
<?php

namespace App\Http\Controllers;

use Northwestern\SysDev\SOA\DirectorySearch;

class MyController extends Controllers
{
    public function login(Request $request, DirectorySearch $directory)
    {
        // Defaults to the expanded version of the API call
        $info = $directory->lookupByNetId('nie7321');
        if ($info == false) {
            dd($directory->getLastError());
        }

        dd($info['mail']);

        // There are other lookup methods available. Anywhere 'basic' is specified, you may also use 'public' or 'expanded'.
        $info = $directory->lookupByNetId('nie7321', 'basic');

        $info = $directory->lookup('1234567', 'emplid', 'basic');
        $info = $directory->lookup('1234567', 'hremplid', 'basic');
        $info = $directory->lookup('1234567', 'sesemplid', 'basic');
        $info = $directory->lookup('1234567', 'barcode', 'basic');
        $info = $directory->lookup('test@northwestern.edu', 'mail', 'basic');
        $info = $directory->lookup('test2020@u.northwestern.edu', 'studentemail', 'basic');
    }
}
```

Refer to the [Directory Search API docs](https://northwestern-apiportal.apigee.io/IDM-Services) for more information about what fields you will receive.

## EventHub
The EventHub SDK comes from [northwestern-sysdev/event-hub-php-sdk](https://github.com/NIT-Administrative-Systems/SysDev-EventHub-PHP-SDK). Please review its documentation for details on using it.

This package takes care of setting the library up for you and Laravel-izing it.

There are three key `.env` settings:

| Setting                                     | Purpose                                                                                                                              |
|---------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------|
| `EVENT_HUB_BASE_URL`                        | The Apigee base URL, e.g. `https://northwestern-dev.apigee.net`                                                                      |
| `EVENT_HUB_API_KEY`                         | Your Apigee API key                                                                                                                  |
| `EVENT_HUB_HMAC_VERIFICATION_SHARED_SECRET` | Only applicable if using webhook delivery for incoming messages. Set this to a random string, e.g. `base64_encode(random_bytes(32))` |

A number of other settings are available to control the HMAC security options, but they are set to reasonable defaults. See the `config/nusoa.php` file if you are interested in these.

```php
<?php

namespace App\Http\Controllers;

use Northwestern\SysDev\SOA\EventHub;

class MyController extends Controllers
{
    public function save(Request $request, EventHub\Topic $api)
    {
        // Save some kind of Very Important Business Object to the DB...
        // . . .
        $model->save();

        // And tell everyone it has been updated!
        $message_id = $api->writeJsonMessage('sysdev.a-topic-name', [
            'application_id' => $model->id,
        ]);
    }
}
```

There are also some Laravel-specific features added: an `eventhub_hmac` middleware that you can apply to a route/controller, some useful artisan commands, and an easy way to deploy your desired webhook configuration.

### Webhook Route Registration
The most straightforward way to consume messages is by registering your routes as webhook endpoints.

```php
<?php

// An example `routes/web.php`

Route::get('/eventhub/get-an-event', function () {
    return view('welcome');
})->eventHubWebhook('etsysdev.some.queue.name');
```

When you make changes, run `php artisan eventhub:webhook:configure`. It will read through your routes and (re)configure all of your webhooks.

If you are using the HMAC middleware & have the settings for it in your `.env` (see below), your secret will be sent to EventHub. If you prefer another authentication type (or additional -- you can use all three), you can specify more options that'll be passed through to the webhook POST/PATCH call:

```php
<?php
// See the EventHub webhook API docs to figure out what to pass for HTTP basic auth or API key auth!

$route->eventHubWebhook('etsysdev.some.queue.name', [
    'securityTypes' => ['APIKEY'],
    'webhookSecurity' => [
        [
            'securityType' => 'APIKEY',
            'topicName' => 'etsysdev.some.queue.name',
            'apiKey' => 'my top secret API key',
            'headerName' => 'x-api-key',
        ],
    ],
]);
```

If you delete a registration, the `eventhub:webhook:configure` command will ask you if you'd like to delete the webhook config.

### `eventhub_hmac` Middleware
To use the middleware, set `EVENT_HUB_HMAC_VERIFICATION_SHARED_SECRET` in your `.env`, and then apply it to a route or controller:

```php
<?php

namespace App\Http\Controllers;

class MyController extends Controllers
{
    public function __construct()
    {
        $this->middleware('eventhub_hmac');
    }
}
```

All incoming requests will require a header with a valid, message-specific signature calculated based on the message and the secret shared with EventHub. This signature should be good enough to serve as the sole authentication method, but you can apply an API key or HTTP basic auth middleware as well.

### EventHub Artisan Commands
The following artisan commands will be available when you install this package.

You can run these with `php artisan <command>`.

| Command                         | Purpose                                                                                     |
|---------------------------------|---------------------------------------------------------------------------------------------|
| eventhub:queue:status           | Show all the queues you can read from & some statistics                                     |
| eventhub:topic:status           | Show all the topics you can write to & who is subscribed                                    |
| eventhub:webhook:status         | Show all your configured webhooks                                                           |
| eventhub:webhook:toggle pause   | Pause all webhooks. Optionally, you can pass a list of queue names to pause just those.     |
| eventhub:webhook:toggle unpause | Unpause all webhooks. Optionally, you can pass a list of queue names to unpause just those. |
| eventhub:webhook:configure      | Publishes the webhook delivery routes configured in your route files with EventHub          |

### Comprehensive Webhook Example
Putting everything together:

```php
<?php
// routes/api.php or whatever

Route::middleware(['eventhub_hmac'])->prefix('eventhub')->group(function () {
    Route::post('consume-queue-a', 'ConsumeController@a')->eventHubWebhook('sysdev.queue.a');
    Route::post('consume-queue-b', 'ConsumeController@b')->eventHubWebhook('sysdev.queue.b');
});
```

Then on your console:

```sh
$ php artisan eventhub:webhook:configure
+-----------------------------------+-----------------------------------------------+--------+
| Queue                             | Endpoint                                      | Active |
+-----------------------------------+-----------------------------------------------+--------+
| sysdev.queue.a                    | http://localhost/api/eventhub/consume-queue-a | Active |
| sysdev.queue.b                    | http://localhost/api/eventhub/consume-queue-b | Active |
+-----------------------------------+-----------------------------------------------+--------+
```

In the dev & test environments, you should have permission to write messages to the queues you're subscribed to via `POST /v1/event-hub/queue/your-queue-name`, so to verify your webhooks are running, open your `php artisan tinker` console:

```php
>>> $q_api->sendTestJsonMessage('sysdev.queue.a', ['application_id' => 123])
=> "ID:052d83908c43-35873-1545317905819-1:1:3:1:1"
```

The test message should be delivered to your app almost immediately.

## Contributing
If you'd like to contribute to the library, you are welcome to submit a pull request!

My ideal architecture (going forward :cold_sweat:) is to write plain-old PHP bindings (so folks can use them in non-Laravel apps) and then have the `NuSoaServiceProvider` inject config & add any other Laravel-specific enhancements.

If there is a service on the API Registry that you'd like SysDev to add support for, please go ahead and open an issue requesting it.
