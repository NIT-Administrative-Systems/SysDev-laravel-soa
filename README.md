# Northwestern SOA Bindings for Laravel
This package provides simple classes for accessing popular SOA services from Laravel applications.

| Service | Prerequisites |
| --- | --- |
| WebSSO | None |
| DirectorySearch | Data steward approval, Apigee API key |
| Generic MQ Publisher [deprecated] | Service account, queues, and Apigee API key |
| Generic MQ Consumer [deprecated] | Service account, queues, and Apigee API key |
| EventHub | Queues, Apigee API Key |

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
The API objects should be injected by the Laravel service container:

```php
<?php

namespace App\Http\Controllers;

use Northwestern\SysDev\SOA\MQ\Publisher;

class MyController extends Controllers
{
    protected $pub;

    public function __construct(MQ\Publisher $pub)
    {
        $this->pub = $pub;
    }

}
```

If you are ever in a spot where injection is unavailable, you can always call `make` yourself. This is particularly handy in the tinker console:

```php
$pub = app()->make(Northwestern\SysDev\SOA\MQ\Publisher::class);
```

For troubleshooting, each API has a `getLastError()` method. `dd()`ing the API object should give you everything you'll need, including the request body and URL.

### WebSSO
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

### DirectorySearch
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

### MQ\Publisher
Using the publisher requires an Apigee API key, as well as a service account with write permission. You will also need to know your topic name.

The `MQ_API_URL`, `MQ_API_KEY`, `MQ_API_USERNAME`, and `MQ_API_PASSWORD` should be set in your `.env` file.

```php
<?php

namespace App\Http\Controllers;

use Northwestern\SysDev\SOA\MQ;

class MyController extends Controllers
{
    public function login(Request $request, MQ\Publisher $pub)
    {
        // Easy way to queue JSON
        $result = $pub->queue(['key' => 'value', 'other_key' => 'value'], 'topic/name');
        if ($result == false) {
            dd($pub->getLastError());
        }

        // Queue arbitrary things (XML, plain text, etc)
        $pub->queueText('<xml>Hello there, message queue!</xml>', 'topic/name');
    }
}
```

### MQ\Consumer
Using the consumer requires an Apige API key, as well as a service account with read permission. You will also need to know your topic name.

The `MQ_API_URL`, `MQ_API_KEY`, `MQ_API_USERNAME`, and `MQ_API_PASSWORD` should be set in your `.env` file.

Because PHP is generally not running as a persistent process, you will need to schedule your queue-consuming code to run. Laravel can manage cronjobs for you via [task scheduling](https://laravel.com/docs/5.5/scheduling).

To facilitate that, we have a console command generator that will stub out a queue checking command for you:

```bash
php artisan make:command:checkQueue CheckMyTopic
```

You will need to fill in a topic name in the `getTopic()` method, and then do something with the message.

If that is a bad fit for your needs, using the consumer directly is straightforward:

```php
<?php

namespace App\Http\Controllers;

use Northwestern\SysDev\SOA\MQ;

class MyController extends Controllers
{
    public function login(Request $request, MQ\Consumer $consumer)
    {
        $msg = $consumer->pullMessage('topic/name');
        if ($msg == false) {
            dd($consumer->getLastError());
        }

        $msg = json_decode($msg, JSON_OBJECT_AS_ARRAY);
    }
}
```

Be aware that when you consume a message, it is immediately removed from the queue.
