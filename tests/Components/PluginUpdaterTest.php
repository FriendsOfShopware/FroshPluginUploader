<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components;

use FroshPluginUploader\Components\Generation\Shopware5\PluginReader;
use FroshPluginUploader\Components\PluginUpdater;
use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Components\SBP\Components\General;
use FroshPluginUploader\Components\StoreJsonLoader;
use FroshPluginUploader\Structs\Image;
use FroshPluginUploader\Structs\Plugin;
use PHPUnit\Framework\TestCase;

class PluginUpdaterTest extends TestCase
{
    public function testSync(): void
    {
        $client = $this->createClient();
        $updater = new PluginUpdater($client);

        $storePlugin = Plugin::make([
            'id' => 1,
            'infos' => [
                [
                    'locale' => [
                        'name' => 'en_GB'
                    ]
                ],
                [
                    'locale' => [
                        'name' => 'de_DE'
                    ]
                ]
            ],
            'localizations' => [],
            'license' => [
                'name' => 'agpl'
            ]
        ]);

        $storeJson = $this->createMock(StoreJsonLoader::class);
        $storeJson->method('applyImageUpdate')->willReturn(true);
        $plugin = $this->createMock(\FroshPluginUploader\Components\Generation\Shopware5\Plugin::class);
        $plugin->method('getResourcesFolderPath')->willReturn(dirname(__DIR__) . '/fixtures/plugins/Shopware5Plugin/Resources/store');
        $plugin->method('hasStoreJson')->willReturn(true);
        $plugin->method('getStoreJson')->willReturn($storeJson);

        $updater->sync($plugin, $storePlugin);

        static::assertSame('<p>Test</p>', $storePlugin->infos[0]->description);
        static::assertSame('<p>Test</p>', $storePlugin->infos[0]->installationManual);
        static::assertSame('Test', $storePlugin->infos[0]->features);
        static::assertSame('Test', $storePlugin->infos[0]->highlights);
    }

    public function testSyncProprietary(): void
    {
        $client = $this->createClient();
        $updater = new PluginUpdater($client);

        $storePlugin = Plugin::make([
            'id' => 1,
            'infos' => [
                [
                    'locale' => [
                        'name' => 'en_GB'
                    ]
                ],
                [
                    'locale' => [
                        'name' => 'de_DE'
                    ]
                ]
            ],
            'localizations' => [],
            'license' => [
                'name' => 'agpl'
            ]
        ]);

        $storeJson = $this->createMock(StoreJsonLoader::class);
        $storeJson->method('applyImageUpdate')->willReturn(true);
        $plugin = $this->createMock(\FroshPluginUploader\Components\Generation\Shopware5\Plugin::class);
        $plugin->method('getResourcesFolderPath')->willReturn(dirname(__DIR__) . '/fixtures/plugins/Shopware5Plugin/Resources/store');
        $plugin->method('hasStoreJson')->willReturn(true);
        $plugin->method('getStoreJson')->willReturn($storeJson);

        $reader = $this->createMock(PluginReader::class);
        $reader->method('getLicense')->willReturn('proprietary');

        $plugin->method('getReader')->willReturn($reader);

        $updater->sync($plugin, $storePlugin);

        static::assertSame('<p>Test</p>', $storePlugin->infos[0]->description);
        static::assertSame('<p>Test</p>', $storePlugin->infos[0]->installationManual);
        static::assertSame('proprietary', $storePlugin->license['name']);
    }

    private function createClient(): Client
    {
        $storeData = json_decode(file_get_contents(dirname(__DIR__) . '/fixtures/store_data.json'), true);
        $plugin = $this->createMock(\FroshPluginUploader\Components\SBP\Components\Plugin::class);
        $plugin->method('put')->withAnyParameters()->willReturn(new Plugin);
        $plugin->method('getImages')->willReturn([Image::make(['id' => 1])]);

        $general = $this->createMock(General::class);
        $general->method('all')->willReturn($storeData);
        $general->method('getLocalizations')->willReturn([
            [
                'name' => 'de-DE'
            ],
            [
                'name' => 'en-GB'
            ]
        ]);

        $client = $this->createMock(Client::class);

        $client->method('General')->willReturn($general);
        $client->method('Plugins')->willReturn($plugin);

        return $client;
    }
}