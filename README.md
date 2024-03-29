# FroshPluginUploader
[![codecov](https://codecov.io/gh/FriendsOfShopware/FroshPluginUploader/branch/master/graph/badge.svg)](https://codecov.io/gh/FriendsOfShopware/FroshPluginUploader)
![PHPUnit](https://github.com/FriendsOfShopware/FroshPluginUploader/workflows/PHPUnit/badge.svg)
[![License](https://img.shields.io/github/license/FriendsOfShopware/FroshPluginUploader.svg)](https://github.com/FriendsOfShopware/FroshPluginUploader/blob/master/license.txt)
[![GitHub closed pull requests](https://img.shields.io/github/issues-pr-closed/FriendsOfShopware/FroshPluginUploader.svg)](https://github.com/FriendsOfShopware/FroshPluginUploader/pulls)
[![Slack](https://img.shields.io/badge/chat-on%20slack-%23ECB22E)](https://slack.shopware.com?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

# This tool is abandoned and will only receive bug fixes. Please move to https://github.com/FriendsOfShopware/shopware-cli

Tool for uploading new plugin releases to Shopware Store.
Required Environment variables:

| Name             	 | Default 	 | Description                                                         	 |
|--------------------|-----------|-----------------------------------------------------------------------|
| ACCOUNT_USER     	 | 	         | Shopware Account e-mail address                                     	 |
| ACCOUNT_PASSWORD 	 | 	         | Shopware Account password                                           	 |

Requirements for Plugin:

* [Shopware 5](https://github.com/FriendsOfShopware/FroshPluginUploader/wiki/Shopware-5-Plugins)
* [Shopware Platform](https://github.com/FriendsOfShopware/FroshPluginUploader/wiki/Shopware-Platform-Plugin)

## Docker

You can use it also using Docker. Don't forget to pass your credentials as env variables.

Example: 
```
❯ docker run --rm -w "/storage" -v (pwd):/storage friendsofshopware/plugin-uploader plugin:validate /storage/FroshAppGoogleSheet.zip

 [OK] Has been successfully validated                                           
```

## Archlinux User Repository (AUR)

Install using AUR package [php-sw-frosh-plugin-uploader](https://aur.archlinux.org/packages/php-sw-frosh-plugin-uploader).

## CI-Integration

See [examples](https://github.com/FriendsOfShopware/FroshPluginUploader/tree/master/examples/ci) folder for how the Uploader could be integrated.

# Using the Commands

## ext:upload

Will upload the zip to the store and triggers a code review.
**Plugin version can be deployed multiple times, which updates the version.**

Valid arguments are:

```
pathToZip - path to the zip file
```

Valid options are:
```
--skipCodeReview - Skip the Code-Review
--skipCodeReviewResult - Skip waiting for Code-Review Result
```


## ext:update

Update Shopware store informations about the plugin. For plugins the files from the `${path}/Resources/store`-folder are used. For apps the store folder should be placed directly in the root folder of the app, i.e. the folder is `${path}/store` furthermore the plugin name and so on are read from the app `manifest.xml`.

Valid arguments are:

```
path - path to the plugin folder
```

For more Information about the Resources/store folder checkout [this](https://github.com/FriendsOfShopware/FroshPluginUploader/wiki/Resources-store-Folder).

## ext:validate

Will check the plugin for validation exceptions for Code Review.

Valid arguments are:

```
pathToZip - path to the zip file
```

Valid options are:
```
--create - Create the plugin in account, if it doesn't exist
```

## ext:list

Shows all plugins in the account with the id, name, latest version and last changed.

## ext:download:resources

Downloads all store resources from store to the given folder.

## ext:zip

Allows to zip the git repository or folder of the plugin.

Valid arguments are:
```
path - path to the directory
branch - Optional: will detect the latest tag, otherwise will use master
```

Valid options are:
```
--strategy - default `git`. `plain` will zip the folder as it is.
--scope - default `false`. `true` will scope the plugin dependencies into a specific namespace using [humbug/php-scoper](https://github.com/humbug/php-scoper). php-scoper has to be available in `$PATH`
```

A .sw-zip-blacklist file can be used to define which files should be deleted before creating the zip. (**Deprecated, will be removed with 0.4.0**)

# FAQ

[Getting Credentials](https://github.com/FriendsOfShopware/FroshPluginUploader/wiki/Getting-Credentials)

[Exception-Codes](https://github.com/FriendsOfShopware/FroshPluginUploader/wiki/PluginsException-Codes)
