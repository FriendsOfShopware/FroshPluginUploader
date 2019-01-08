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
* changelogs filled in plugin.xml

# Using the Commands

## plugin:upload

Will upload the zip to the store and triggers a code review

Valid arguments are:

```
pathToZip - path to the zip file
```
