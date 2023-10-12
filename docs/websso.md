# WebSSO
The package provides a command that will set up WebSSO, and optionally Duo multi-factor authentication (MFA). 

You can use either the traditional Online Passport (handled via agentless SSO with OpenAM/ForgeRock), Azure AD SSO, or both at once. 

The package will:

- Create an SSO controller in `App\Http\Controllers\Auth`
- Adds named routes to your `web/routes.php`

The approach taken is flexible. It is suited for both applications that only use WebSSO *and* applications with multiple login methods.

All of the above will still rely on the built-in Laravel `auth` middleware.

:::warning Notes for Advanced Users
Authentication is achieved by logging users into Laravel; once the webSSO session is validated, your user's login session for your application is detached from the webSSO session.

The package does not implement a custom [auth provider](https://laravel.com/docs/5.8/authentication#adding-custom-user-providers) and relies on the default database provider for the `App\Models\User` model.
:::

## Prerequisites
### Online Passport (OpenAM/ForgeRock)
You will need an Apigee key with access to the `IDM - Agentless WebSSO`. The key will include access to the SSO & MFA API. This must be requested through the [API service registry](https://apiserviceregistry.northwestern.edu/). 

Your application must be served over HTTPS on a `northwestern.edu` domain. The SSO cookie (`nusso`) is flagged as Secure=true; there is no way for Laravel to access the cookie when served over an insecure http connection.

### Azure AD
You will need to register an application in the [Azure control panel](https://portal.azure.com/#blade/Microsoft_AAD_IAM/ActiveDirectoryMenuBlade/RegisteredApps), register your callback URL, and generate a secret. Creating and managing an Azure AD app is [mostly] self-service.

Callback URLs must be served over HTTPS. It does not need to be on any specific domain. Please see [the Azure documentation](https://docs.microsoft.com/en-us/azure/active-directory/develop/reply-url) for more information about acceptable callback URLs. 

If you wish to use MFA with Azure AD, you must send a ticket to Collab Services asking them to enable it for your application. You do not need to make any configuration or code changes to enable it.

The default Laravel cache driver will be used to store Microsoft's JWT signing keys. These are loaded on demand and stored for a few minutes.

## Setting up SSO
Getting webSSO working should only take a few minutes. For both Online Passport and Azure AD, start by running:

```
php artisan make:websso
```

### Online Passport
To configure Online Passport, add the following to your `.env`:

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

### Azure AD
To configure Azure AD, add the following to your `config/services.php`:

```php
'northwestern-azure' => [
    'client_id' => env('AZURE_CLIENT_ID'),
    'client_secret' => env('AZURE_CLIENT_SECRET'),
    'redirect' => env('AZURE_REDIRECT_URI') // will be determined at runtime
    
    /**
     * These parameters can be changed for multi-tenant app registrations.
     * They will default to Northwestern's tenant ID and our domain hint.
     * 
     * In most use-cases, these will not be used.
     */ 
    // 'tenant' => 'common',
    // 'domain_hint' => null,
],
```

At this point, you will need to have created an application in Azure AD and generated a secret for it. 

You must register a callback URI in Azure AD as well. The correct URL to register is the route named `login-oauth-callback`. You can run `php artisan websso:callback` to see the whole URL.

Add the client ID and secret to your `.env` file:

```ini
# This is the 'Application (client) ID' on the app's overview page in Azure
AZURE_CLIENT_ID=

# This is the value of a client secret from the 'Certificates & secrets' page in Azure
AZURE_CLIENT_SECRET=
```

### Resolving Users
Reviewing the setup and adding code to resolve users must be completed for both Online Passport and Azure AD.

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

:::tip Azure AD Profile
If you are using Azure AD and want to utilize the profile information like email address & phone number, you can instead implement the `findUserByOAuthUser` method. 

Similar to `findUserByNetID`, you can request dependencies from the service container.

This method is only called for Azure AD SSO.
:::

## Signing On
To get your users signing in, you need to redirect them to one of the following routes:

| Route Name             | Type            |
|------------------------|-----------------|
| `login`                | Online Passport |
| `login-oauth-redirect` | Azure AD        |

This can be either the user clicking a login link, or the `App\Http\Middleware\Authenticate` middleware redirecting unauthenticated users to one of these routes. 

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
The webSSO class will resolve the value of an `nusso` cookie into a NetID using the agentless SSO APIs.

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
