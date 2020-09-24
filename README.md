# FroshPluginUploader
[![codecov](https://codecov.io/gh/FriendsOfShopware/FroshPluginUploader/branch/master/graph/badge.svg)](https://codecov.io/gh/FriendsOfShopware/FroshPluginUploader)
![PHPUnit](https://github.com/FriendsOfShopware/FroshPluginUploader/workflows/PHPUnit/badge.svg)
[![License](https://img.shields.io/github/license/FriendsOfShopware/FroshPluginUploader.svg)](https://github.com/FriendsOfShopware/FroshPluginUploader/blob/master/license.txt)
[![GitHub closed pull requests](https://img.shields.io/github/issues-pr-closed/FriendsOfShopware/FroshPluginUploader.svg)](https://github.com/FriendsOfShopware/FroshPluginUploader/pulls)
[![Slack](https://img.shields.io/badge/chat-on%20slack-%23ECB22E)](https://slack.shopware.com?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

Tool for uploading new plugin releases to Shopware Store

**This Tool works only for the new plugin system, Shopware Platform and app system**

Required Environment variables

| Name             	| Default 	| Description                                                         	|
|------------------	|---------	|---------------------------------------------------------------------	|
| ACCOUNT_USER     	|         	| Shopware ID                                                         	|
| ACCOUNT_PASSWORD 	|         	| Shopware ID password                                                	|
| ~~PLUGIN_ID~~    	|         	| Removed with Version 0.3.0 	                                        |

Requirements for Plugin

* [Shopware 5](https://github.com/FriendsOfShopware/FroshPluginUploader/wiki/Shopware-5-Plugins)
* [Shopware Platform](https://github.com/FriendsOfShopware/FroshPluginUploader/wiki/Shopware-Platform-Plugin)

## Docker

You can use it also using Docker. Don't forget to pass your credentials as env variables.

Example: 
```
❯ docker run --rm -v (pwd):/storage friendsofshopware/plugin-uploader plugin:validate /storage/FroshAppGoogleSheet.zip

 [OK] Has been successfully validated                                           
```

## CI-Integration

See [examples](https://github.com/FriendsOfShopware/FroshPluginUploader/tree/master/examples/ci) folder for how the Uploader could be integrated.

# Using the Commands

## ext:upload

Will upload the zip to the store and triggers a code review.
**Plugin version can be deployed multiple times, which updates the version**

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

Will update informations about the plugin from the `Resources/store`-folder

Valid arguments are:

```
path - path to the plugin folder
```

For more Information about the Resources/store folder checkout [this](https://github.com/FriendsOfShopware/FroshPluginUploader/wiki/Resources-store-Folder)

## ext:validate

Will check the plugin for validation exceptions for Code Review

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

Downloads all store resources from store to the given folder

## ext:zip

Allows to zip the git repository or folder of the plugin

Valid arguments are:
```
path - path to the directory
branch - Optional: will detect the latest tag, otherwise will use master
```

Valid options are:
```
--strategy - default `git`. `plain` will zip the folder as it is.
```

A .sw-zip-blacklist file can be used to define which files should be deleted before creating the zip. (**Deprecated, will be removed with 0.4.0**)

# FAQ

[Exception-Codes](https://github.com/FriendsOfShopware/FroshPluginUploader/wiki/PluginsException-Codes)
