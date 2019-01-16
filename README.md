# FroshPluginUploader

Tool for uploading new plugin releases to Shopware Store

**This Tool works only for the new plugin system**

Required Enviroment variables

| Name             	| Default 	| Description                                                         	|
|------------------	|---------	|---------------------------------------------------------------------	|
| PLUGIN_ID        	|         	| Plugin ID from account2.shopware.com. Can be obtained from the link 	|
| ACCOUNT_USER     	|         	| Shopware ID                                                         	|
| ACCOUNT_PASSWORD 	|         	| Shopware ID password                                                	|

Requirements of the Plugin

* 5.2 Plugin System
* compability tag in plugin.xml
* changelogs filled in plugin.xml (german and english)

# Using the Commands

## plugin:upload

Will upload the zip to the store and triggers a code review

Valid arguments are:

```
pathToZip - path to the zip file
```


## plugin:update

Will update some informations about the plugin like description and images

Valid arguments are:

```
path - path to the /Resources/store
```

Currently supported files:

* [lang].html (e.g de.html) for Description
* [lang]_manual.html (e.g de_manual.html) for Install Instruction
* images/*.(png|jpg|jpeg) will be used for Images. First image will be used as preview image.

## plugin:validate

Will check the plugin for validation exceptions for Code Review

Valid arguments are:

```
pathToZip - path to the zip file
```
