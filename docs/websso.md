# WebSSO
The package provides a command that will set up WebSSO, and optionally Duo multi-factor authentication (MFA). 

- Create SSO & Duo controllers in `App\Http\Controllers\Auth`
- Adds named routes to your `web/routes.php`
- Ejects a `resources/views/auth/mfa.blade.php` template for rendering the Duo MFA widget

The approach taken is flexible. It is suited for both applications that only use WebSSO *and* applications with multiple login methods.

All of the above will still rely on the built-in Laravel `auth` middleware.

:::warning Notes for Advanced Users
Authentication is achieved by logging users into Laravel; once the webSSO session is validated, your user's login session for your application is detached from the webSSO session.

The package does not implement a custom [auth provider](https://laravel.com/docs/5.8/authentication#adding-custom-user-providers) and relies on the default database provider for the `App\User` model.
:::

## Setting up SSO
Getting webSSO working should only take a few minutes.

```
php artisan make:websso
```

First, review your `routes/web.php`. You can adjust the paths, if desired.

Then, open up `App\Http\Controllers\Auth\WebSSOController` and implement the `findUserByNetID` method. 

It needs to return an object that implements the `Authenticatable` interface. The `App\User` model that Laravel comes with satisfies this requirement. 

If you return `null` from this method, the login will fail. This may be desired in cases where only certain pre-approved users are permitted to log in.

```php
use App\User;

protected function findUserByNetID(string $netid): ?Authenticatable
{
    // If the user exists, they can log in.
    $user = User::where('netid', $netid)->first();
    if ($user !== null) {
        return $user;
    }

    // If you have a Directory Search API key, you could grab info about them & create a user.
    $directory = $this->directory_api->lookupNetId($netid, 'basic');
    $user = User::create([
        'name' => $netid,
        'email' => $directory['mail'],
    ]);

    return $user;
}
```

You may optionally implement the `authenticated` method. If you return a `redirect()`, it will be followed. Otherwise, the default Laravel behaviour will be used.

## Enabling Duo MFA
If you want to enable Duo MFA, you will need to submit a ticket to Identity Services team via [consultant@northwestern.edu](mailto:consultant@northwestern.edu):

> Hi Identity Services,
>
>Can you create a new Web SDK application for us in Duo, called [application or project name]?
>
>We need the following info from you once it's created:
>
>- Integration key (IKEY)
>- Secret key (SKEY)
>- API URL
> 
>Thank you very much!

Once you have recieved this information, you must generated an application key (AKEY). Duo recommends a 40+ character string, which you can create by running `bin2hex(random_bytes(32))` in the tinker console.

With these four config items in hand, update your `.env`:

```
DUO_ENABLED=true
DUO_IKEY=
DUO_SKEY=
DUO_AKEY=
DUO_URL=
```

You may need to update the `resources/views/auth/mfa.blade.php` file to fit your site layout; it assumes the default Laravel `layouts.app` template is being used.

Duo should now be enabled.

## API
The webSSO class will resolve the value of an `openAMssoToken` cookie into a NetID.

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