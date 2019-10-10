# EventHub
This sets up the [EventHub SDK for PHP](https://github.com/NIT-Administrative-Systems/SysDev-EventHub-PHP-SDK) for use with Larave, adds commands to manage topics, queues, and webhooks from the console, and the `eventhub_hmac` middleware for authenticating webhook-delivered events.

There are three key `.env` settings:

| Setting                                     | Purpose                                                                                                     |
|---------------------------------------------|-------------------------------------------------------------------------------------------------------------|
| `EVENT_HUB_BASE_URL`                        | The Apigee base URL, e.g. `https://northwestern-dev.apigee.net`                                             |
| `EVENT_HUB_API_KEY`                         | Your Apigee API key                                                                                         |
| `EVENT_HUB_HMAC_VERIFICATION_SHARED_SECRET` | Only applicable for consuming messages. Set this to a random string, e.g. `base64_encode(random_bytes(32))` |

A number of other settings are available to control the HMAC security options, but they are set to reasonable defaults. See the `config/nusoa.php` file if you are interested in these.

You can review your available EventHub queues & topics from the console:

```
php artisan eventhub:queue:status
php artisan eventhub:topic:status
```

## Sending Messages
```php
<?php

namespace App\Http\Controllers;

use Northwestern\SysDev\SOA\EventHub;

class MyController extends Controllers
{
    public function save(Request $request, EventHub\Topic $api)
    {
        $message_id = $api->writeJsonMessage('my-team.a-topic-name', [
            'id' => 123,
        ]);
    }
}
```

## Consuming Messages
The best way to consume EventHub messages is by setting up webhooks & allowing EventHub to deliver messages to your application in real-time. 

This package makes receiving webhooks easy: register a queue name to a route, apply the `eventhub_hmac` middleware to its controller, and do something with the message:

```php
<?php

// In your `routes/web.php`
Route::post('events/netid-update', 'NetIdUpdateController')->eventHubWebhook('my-team.ldap.netid.term');
Route::post('events/employee-update', 'EmployeeUpdateController')->eventHubWebhook('my-team.employee.updates', ['contentType' => 'application/xml']); // for XML messages

// App\Http\Controllers\NetIdUpdateController
use Illuminate\Http\Request;

class NetIdUpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware('eventhub_hmac');
    }

    public function __invoke(Request $request)
    {
        $raw_json = $request->getContent();

        // . . .
    }
}
```

Finally, run `php artisan eventhub:webhook:configure`. It will read through your routes and make the API calls to EventHub that (re)configure all of your webhooks. If you delete a registration, the `eventhub:webhook:configure` command will ask you if you'd like to delete the webhook config.

:::tip App Deployments
It is recommended that you pause webhook deliveries when you deploy updates to your application. 

```sh
php artisan eventhub:webhook:toggle pause

# Do the deployment ...

# Running configure after deploying makes sure the settings
# in EventHub still match your registered webhook routes.
php artisan eventhub:webhook:configure
php artisan eventhub:webhook:toggle unpause
```

EventHub will retry failed message deliveries for time time if you have forgotten to pause. See the EventHub documentation for more information on delivery re-tries.
:::

## EventHub Artisan Commands
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

## Test Messages
In the dev & test environments, you should have permission to write messages to the queues you're subscribed to via `POST /v1/event-hub/queue/your-queue-name`. 

You can verify your message processing code is working from the tinker console:

```php
>>> $q_api = resolve(\Northwestern\SysDev\SOA\EventHub\Queue::class);
=> Northwestern\SysDev\SOA\EventHub\Queue

>>> $q_api->sendTestJsonMessage('sysdev.queue.a', ['application_id' => 123])
=> "ID:052d83908c43-35873-1545317905819-1:1:3:1:1"
```

When using webhooks, the test message should be delivered to your app immediately.