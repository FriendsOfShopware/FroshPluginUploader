# FroshPluginUploader

Tool for uploading new plugin releases to Shopware Store

**This Tool works only for the new plugin system or Shopware Platform**

Required Environment variables

| Name             	| Default 	| Description                                                         	|
|------------------	|---------	|---------------------------------------------------------------------	|
| ACCOUNT_USER     	|         	| Shopware ID                                                         	|
| ACCOUNT_PASSWORD 	|         	| Shopware ID password                                                	|
| ~~PLUGIN_ID~~    	|         	| Removed with Version 0.3.0 	                                        |

Requirements for Plugin

* [Shopware 5](https://github.com/FriendsOfShopware/FroshPluginUploader/wiki/Shopware-5-Plugins)
* [Shopware Platform](https://github.com/FriendsOfShopware/FroshPluginUploader/wiki/Shopware-Platform-Plugin)

# Using the Commands

## plugin:upload

Will upload the zip to the store and triggers a code review.
**Plugin version can be deployed multiple times. It will be updated then**

Valid arguments are:

```
pathToZip - path to the zip file
```

Valid options are:

```
--skipCodeReviewResult - Skip waiting for Code-Review Result
```


## plugin:update

Will update informations about the plugin from you `Resources/store`-folder

Valid arguments are:

```
path - path to the plugin folder
```

For more Information about the Resources/store folder checkout [this](https://github.com/FriendsOfShopware/FroshPluginUploader/wiki/Resources-store-Folder)

## plugin:validate

Will check the plugin for validation exceptions for Code Review

Valid arguments are:

```
pathToZip - path to the zip file
```

## plugin:list

Shows all plugins in the account with the id, name, latest version and last changed.

## plugin:download:resources

Downloads all store resources from store to the given folder

## plugin:zip:dir

Allows to zip the git repository of the plugin

```
gitPath - path to the git repository
branch - Optional: will detect the latest tag, otherwise will use master
```

A .sw-zip-blacklist file can be used to define which files should be deleted before creating the zip

# FAQ

[Exception-Codes](https://github.com/FriendsOfShopware/FroshPluginUploader/wiki/PluginsException-Codes)
