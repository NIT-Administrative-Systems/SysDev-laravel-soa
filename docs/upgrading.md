# Upgrading

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