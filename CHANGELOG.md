# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/Symplify/ChangelogLinker).

<!-- changelog-linker -->

## [0.3.3] - 2020-09-24

- [#75] NRFE-3714: add optional scoping of extension, Thanks to [@powli]
- [#49] add check for length of description, Thanks to [@tinect]
- [#81] Update symplify/changelog-linker requirement from ^8.2 to ^8.3, Thanks to [@dependabot-preview][bot]
- [#77] Update composer/semver requirement from ^3.1 to ^3.2, Thanks to [@dependabot-preview][bot]
- [#76] Update composer/semver requirement from ^3.0 to ^3.1, Thanks to [@dependabot-preview][bot]
- [#73] Update symplify/changelog-linker requirement from ^8.1 to ^8.2, Thanks to [@dependabot-preview][bot]
- [#72] Update phpunit/phpunit requirement from ^9.2 to ^9.3, Thanks to [@dependabot-preview][bot]
- [#71] Update guzzlehttp/guzzle requirement from ~6.5 to ~7.0, Thanks to [@dependabot-preview][bot]
- [#68] Update phpunit/phpunit requirement from ^8.5 to ^9.2, Thanks to [@dependabot-preview][bot]
- [#67] Update guzzlehttp/guzzle requirement from ~6.0 to ~6.5, Thanks to [@dependabot-preview][bot]
- [#66] Update nette/php-generator requirement from ^3.2 to ^3.4, Thanks to [@dependabot-preview][bot]
- [#65] Update phpunit/phpunit requirement from ^8.0 to ^8.5, Thanks to [@dependabot-preview][bot]
- [#82] Fixes [#74], Thanks to [@powli]
- [#79] [#78] fixed plugin.xml and plugin.png validation for shopware 5, Thanks to [@ascheider]
- Increased minimum PHP Version to 7.4
- [#61] Improve plugin validation, fixes [#52], [#53], [#54], [#55], [#56], Thanks to [@shyim]
- [#63] Add more tests, Thanks to [@shyim]
- [#64] remove files that are not allowed in store, Thanks to [@tinect]
- [#49] add check for length of description, Thanks to [@tinect]
- [#60] Apps needs a license in manifest, fixes [#48], Thanks to [@shyim]

<!-- dumped content end -->

<!-- dumped content start -->

## [0.3.2] - 2020-07-15

- [#59] Fix composer install while zipping, Thanks to [@shyim]
- [#58] Fix search for plugins with identical name, Thanks to [@shyim]

## [0.3.1] - 2020-07-09

- [#44] Add language inheritance to Shopware5 Reader, Thanks to [@shyim]
- [#47] Update docker image, Thanks to [@shyim]
- [#45] Deprecate .sw-zip-blacklist. Use .gitattributes instead, Thanks to [@shyim]
- [#50] fix new store license, Thanks to [@tinect]
- [#46] Remove deprecated composer.json fields for Shopware 6, fixes [#41], Thanks to [@shyim]

<!-- dumped content end -->

<!-- dumped content start -->

## [0.3.0]

- [#38] Added option to create extension when does not exists., Thanks to [@shyim]
- [#16] added min version support in composer.json, Thanks to [@bilobait-lorenz]
- [#40] Update README.md, Thanks to [@tinect]
- [#27] Improve plugin zip by name detection, Thanks to [@JoshuaBehrens]
- [#15] Clean branch name, Thanks to [@runelaenen]
- [#18] [#17]: Omit Shopware base packages in plugin .zip, Thanks to [@moehrenzahn]
- [#39] Refactor Util class out, Thanks to [@shyim]
- [#24] Update README.md, Thanks to [@tinect]
- [#31] Increase code-review wait timeout, Thanks to [@shyim]
- [#35] Allow using plain copying files in plugin zipping, Thanks to [@shyim]
- [#37] Allow uploading app system extensions, Thanks to [@shyim]
- [#32] Fix plugin zipping in same folder, Thanks to [@shyim]
- [#33] Remove required PLUGIN_ID, fixes [#21], Thanks to [@shyim]
- [#34] Allow configuring image options in store.json, fixes [#20], Thanks to [@shyim]
- Fix listing of plugins
- Update plugin icon after updated plugin

## [0.2.2] - 2020-01-19

- [#14] Test Github Actions, Thanks to [@shyim]
- [#12] Check for store.json, Thanks to [@bilobait-lorenz]
- [#13] removed check for keywords in composer.json, Thanks to [@bilobait-lorenz]

## [0.2.1] - 2019-05-29

- [#11] check for major version when collection compatible shop versions, Thanks to [@bilobait-lorenz]

## [0.1.2] - 2019-05-14

- [#5] added dotenv check and .env.example, Thanks to [@Dwza]
- [#9] Support for plugin features & highlights, Thanks to [@bilobait-lorenz]
- [#6] More infos in list command, Thanks to [@Dwza]

<!-- dumped content end -->

[#40]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/40
[#39]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/39
[#38]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/38
[#37]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/37
[#35]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/35
[#34]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/34
[#33]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/33
[#32]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/32
[#31]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/31
[#27]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/27
[#24]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/24
[#21]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/21
[#20]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/20
[#18]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/18
[#17]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/17
[#16]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/16
[#15]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/15
[#14]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/14
[#13]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/13
[#12]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/12
[#11]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/11
[#9]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/9
[#6]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/6
[#5]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/5
[@tinect]: https://github.com/tinect
[@shyim]: https://github.com/shyim
[@runelaenen]: https://github.com/runelaenen
[@moehrenzahn]: https://github.com/moehrenzahn
[@bilobait-lorenz]: https://github.com/bilobait-lorenz
[@JoshuaBehrens]: https://github.com/JoshuaBehrens
[@Dwza]: https://github.com/Dwza
[0.3.0]: https://github.com/FriendsOfShopware/FroshPluginUploader/compare/0.2.2...0.3.0
[0.2.2]: https://github.com/FriendsOfShopware/FroshPluginUploader/compare/0.2.1...0.2.2
[0.2.1]: https://github.com/FriendsOfShopware/FroshPluginUploader/compare/0.1.2...0.2.1
[#59]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/59
[#58]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/58
[#50]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/50
[#47]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/47
[#46]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/46
[#45]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/45
[#44]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/44
[#41]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/41
[0.3.2]: https://github.com/FriendsOfShopware/FroshPluginUploader/compare/0.3.1...0.3.2
[0.3.1]: https://github.com/FriendsOfShopware/FroshPluginUploader/compare/0.3.0...0.3.1
[#60]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/60
[#48]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/48
[#61]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/61
[#56]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/56
[#55]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/55
[#54]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/54
[#53]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/53
[#52]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/52
[#63]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/63
[#64]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/64
[#49]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/49
[#82]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/82
[#81]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/81
[#79]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/79
[#78]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/78
[#77]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/77
[#76]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/76
[#75]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/75
[#74]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/74
[#73]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/73
[#72]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/72
[#71]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/71
[#68]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/68
[#67]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/67
[#66]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/66
[#65]: https://github.com/FriendsOfShopware/FroshPluginUploader/pull/65
[@powli]: https://github.com/powli
[@dependabot-preview]: https://github.com/dependabot-preview
[@ascheider]: https://github.com/ascheider
