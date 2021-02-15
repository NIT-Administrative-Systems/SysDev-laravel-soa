# WebSSO
The package provides a command that will set up WebSSO, and optionally Duo multi-factor authentication (MFA). 

- Create SSO & Duo controllers in `App\Http\Controllers\Auth`
- Adds named routes to your `web/routes.php`

The approach taken is flexible. It is suited for both applications that only use WebSSO *and* applications with multiple login methods.

All of the above will still rely on the built-in Laravel `auth` middleware.

:::warning Notes for Advanced Users
Authentication is achieved by logging users into Laravel; once the webSSO session is validated, your user's login session for your application is detached from the webSSO session.

The package does not implement a custom [auth provider](https://laravel.com/docs/5.8/authentication#adding-custom-user-providers) and relies on the default database provider for the `App\User` model.
:::

## Prerequisites
You will need an Apigee key with access to the `IDM - Agentless WebSSO`. The key will include access to the SSO & MFA API. 

This must be requested through the [API service registry](https://apiserviceregistry.northwestern.edu/).

## Setting up SSO
Getting webSSO working should only take a few minutes.

```
php artisan make:websso
```

First, add the following to your `.env`:

```ini
WEBSSO_API_KEY=YOUR_APIGEE_API_KEY

# Prod would be https://prd-nusso.it.northwestern.edu
WEBSSO_URL_BASE=https://uat-nusso.it.northwestern.edu

# Prod would be https://northwestern-prod.apigee.net/agentless-websso
WEBSSO_API_URL_BASE=https://northwestern-test.apigee.net/agentless-websso

# Controls whether or not MFA will be required
# You should enable MFA, unless there's a good reason not to!
DUO_ENABLED=true
```

Review your `routes/web.php`. You can adjust the paths, if desired.

Then, open up `App\Http\Controllers\Auth\WebSSOController` and implement the `findUserByNetID` method. You may inject any additional dependencies (e.g. `DirectorySearch`) you need in this method.

It needs to return an object that implements the `Authenticatable` interface. The `App\User` model that Laravel comes with satisfies this requirement. 

If you return `null` from this method, the login will fail. This may be desired in cases where only certain pre-approved users are permitted to log in.

```php
use App\User;

protected function findUserByNetID(DirectorySearch $directory_api, string $netid): ?Authenticatable
{
    // If the user exists, they can log in.
    $user = User::where('netid', $netid)->first();
    if ($user !== null) {
        return $user;
    }

    // If you have a Directory Search API key, you could grab info about them & create a user.
    $directory = $directory_api->lookupNetId($netid, 'basic');
    $user = User::create([
        'name' => $netid,
        'email' => $directory['mail'],
    ]);

    return $user;
}
```

You may optionally implement the `authenticated` method. If you return a `redirect()`, it will be followed. Otherwise, the default Laravel behaviour will be used.

## Changing Routes
The default route names `login` & `logout` are used by the controller traits.

If you want to rename these routes, you will need to override these properties in both controllers.

There is a fourth property, `logout_return_to_route`, that controls where the WebSSO logout page will send users. In an application that only uses WebSSO for logins, you can leave this `null`.

```php
class WebSSOController extends Controller
{
    public function __construct()
    {
        $this->login_route_name = 'login';
        $this->logout_route_name = 'logout';

        $this->logout_return_to_route = null;
    }

    // . . .
}
```

If you are only using WebSSO to authenticate in your app, this should not be necessary. If you have multiple login methods, you will either need to rename the routes, or update your `App\Http\Middleware\Authenticate` to send unauthenticated users to page that lets them choose their login method.

## API
The webSSO class will resolve the value of an `nusso` cookie into a NetID.

:::tip Unusual Use-cases Only
If you have set up the authentication controllers as detailed in [the previous section](#authentication-flow), you should not need to use the `WebSSO` class yourself.
:::

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
        //
        // You can add cookie names to the EncryptCookies middleware's $except property to get around that,
        // but for our example, $_COOKIE works just fine.
        $token = $_COOKIE['nusso'];

        $user = $sso->getUser($token);
        if ($user == null) {
            redirect('sso login page url here');
        }

        dd($user->getNetid()); // netID as a string with no frills
    }
}
```
