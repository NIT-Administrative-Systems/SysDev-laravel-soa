# Upgrading

## From v10 to v11
When upgrading to Laravel 11 from a previous version, if you have applied the Laravel skeleton simplifications, you will need to update the Azure AD callback route when deleting the `\App\Http\Middleware\VerifyCsrfToken`:

```diff
diff --git a/stubs/routes.stub b/stubs/routes.stub
index 34d4712..c65c785 100644
--- a/stubs/routes.stub
+++ b/stubs/routes.stub
@@ -5,6 +5,6 @@ Route::get('auth/logout', [\App\Controllers\Auth\WebSSOController::class, 'logou
 Route::group(['prefix' => 'auth/azure-ad'], function () {
     Route::get('redirect', [\App\Controllers\Auth\WebSSOController::class, 'oauthRedirect'])->name('login-oauth-redirect');
     Route::post('callback', [\App\Controllers\Auth\WebSSOController::class, 'oauthCallback'])->name('login-oauth-callback')
-        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
+        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
     Route::post('oauth-logout', [\App\Controllers\Auth\WebSSOController::class, 'oauthLogout'])->name('login-oauth-logout');
 });
```

## From v9 to v10
PHP 7.4 & 8.0 support has been dropped.

Some types have been updated. The one that you are likely to run into is `DirectorySearch::lookup(...): array|false`. If you had provided a type when extending this class, a return type of `array|bool` will not be compatible.

## From v8 to v9
PHP 7.3 support was dropped due to necessary dependencies dropping support.

There are no changes required.

## From v8.2 to v8.3
Support for logging out via Azure AD SSO was added.

There is a new route, which belongs in the group with the other Azure AD routes:

```php
Route::post('oauth-logout', [\App\Controllers\Auth\WebSSOController::class, 'oauthLogout'])->name('login-oauth-logout');
```

## From v7 to v8
Support for Azure AD SSO was added. This is compatible with the OpenAM/ForgeRock Online Passport SSO, and can be used in tandem.

For information on setting up an Azure AD integration, review the updated [webSSO page](./websso.md).

### Breaking Changes
- The `WebSSOController::findUserByNetID()` method will now always receive the `$netid` parameter in lower case. Previously, it was whatever case the API returned.

## From v6 to v7
Support for older versions of PHP has been discontinued. v7 requires PHP 7.4 or higher. You can continue to use an older version of the package if you are using an older version of PHP.

The `WEBSSO_STRATEGY=classic` option has been removed entirely. 

The dependency on Duo's PHP SDK, along with supporting code for doing Duo authentication in your own app, has been removed. The newer webSSO login flow includes the Duo prompt; your application no longer has to present the widget.

- If you have ejected the `config/duo.php` file, you can remove the file. 
- If you have the `mfa_route_name` route overwritten per [the webSSO Changing Routes guide](websso.md#changing-routes), you can remove the line of code. 
- If you have `Route::resource('auth/mfa', 'Auth\DuoController')->only(['index', 'store']);` in your `routes/web.php` file, you can remove the line of code.
- If you have an `Http\Controllers\Auth\DuoController` controller, you can remove the file.

### Breaking Changes
- The `Northwestern\SysDev\SOA\Auth\WebSSOAuthentication` trait's `findUserByNetID` method was previously abstract. It is now defined in the trait, but throws an exception if it is not re-defined in your application.

  This change should not have any practical impact to your application. 
  
  It is necessary for PHP 8 compatability; asking the service container for additional variables beyond the `string $netid` parameter defined in the method signature would cause the runtime to error out, since [PHP 8 now raises a fatal error instead of a warning](https://php.watch/versions/8.0/lsp-errors#lsp) when an abstract method's parameters differ.

- If you have implemented a custom `Northwestern\SysDev\SOA\Auth\Strategy\WebSSOStrategy`, the login method no longer takes the `string $mfa_route_name` parameter. The new method signature is as follows:

  `public function login(Request $request, string $login_route_name);`

## From v5 to v6
v6 changes the default webSSO strategy from the legacy webSSO system to the newer one. This is a breaking change that merits a major version bump, but if you have already switched (and most systems have) then this release can be treated as a minor upgrade.

The `USE_NEW_WEBSSO_SERVER` environment variable has been removed. If you want to use the older webSSO system, you can configure your system like this:

```ini
WEBSSO_STRATEGY=classic
WEBSSO_URL_BASE=https://websso.it.northwestern.edu
```

Using the new webSSO is unchanged. You should remove the `USE_NEW_WEBSSO_SERVER=true` environment variable to avoid confusion in the future, but it won't hurt anything.

If your app has the ejected `resources/views/auth/mfa.blade.php` file, it can be removed. The new webSSO handles the MFA prompt during its login flow, so this view is no longer used.

## From v4 to v5
v5 is a compatability release for Laravel 7, and drops support for older versions of Laravel. Users on Laravel 5.x & 6 may continue to use v4.

If you do not already have the dependency, the `laravel/ui` package is now required.

## From v3 to v4
This release adds opt-in support for the new webSSO on OpenAM 11. Code supporting the old webSSO system has been marked as deprecated and will be removed after the project is compelete.

You will need to update the `config/nusoa.php` with additional options in the `sso` section. Please [review the config file](https://github.com/NIT-Administrative-Systems/SysDev-laravel-soa/blob/master/config/nusoa.php) and add the new options.

### Using New WebSSO
If you are using the provided webSSO login workflow from `php artisan make:sso`, you can easily swap between the old and new webSSO systems with the following environment variables:

```ini
USE_NEW_WEBSSO_SERVER=true

# If you're not using new SSO, you do not need to set these.
WEBSSO_URL_BASE=https://uat-nusso.it.northwestern.edu
WEBSSO_API_URL_BASE=https://northwestern-test.apigee.net/agentless-websso
WEBSSO_API_KEY=your-apikey-here
```

This is the recommended upgrade path; it allows you to deploy support in advance and easily migrate back and forth as needed.

:::danger HTTPS Required
The new webSSO sets the `secure` flag on its cookie. Your development site **must** be served over HTTPS in order to work.

If you hit a redirect loop when logging in to your app after switching, verify that your site is being served via HTTPS.
:::

The multi-factor authentication step will be handled by the webSSO server. You do not need Duo integration keys after you have moved to the new webSSO. The `DUO_ENABLED` environment variable still controls whether or not you want multi-factor authentication.

### Breaking Changes
Changes have been made to the underlying `WebSSO` class. You only need to worry about that if you are using `Northwestern\SysDev\SOA\WebSSO` directly. The `php artisan make:sso` login workflow has been updated for you, and you will not need to make any changes to your login/mfa controllers.

- `WebSSO` is now an interface. If you are doing `new WebSSO`, please use dependency injection instead.
- The `getNetID()` previously could return a string or `false`. It now returns a string or `null`.
- The `getNetID()` method has been marked as deprecated. A new `getUser()` method replaces this, which returns an object that contains the netID & more information.

## From v2 to v3
v3 adds the SSO & Duo drop-in auth controllers. You are not required to use this feature, and any webSSO implementations that depend on v2 of package should continue to work. If you want to take advantage of the new webSSO drop-in auth controllers, instructions are available [on the webSSO page](./websso).

The `eventhub:webhook:configure` command now has a `--force` flag that will skip the delete confirmation for extra webhooks.

### Breaking Changes
- There is one potential breaking change: the `WebSSO` class incorrectly depended on `config('sso.openAmBaseUrl')`. 

  This has been updated to `config('nusoa.sso.openAmBaseUrl')`.

  If you had a `config/sso.php` file as a workaround for this bug, you can probably delete it.

## From v1 to v2
The MQ Consumer & Publishers have been replaced by EventHub. This is a radical change, as the underlying messaging service we use has changed. 

Please see the [EventHub](./eventhub) article for usage instructions.