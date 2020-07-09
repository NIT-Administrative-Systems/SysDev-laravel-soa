# Upgrading

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