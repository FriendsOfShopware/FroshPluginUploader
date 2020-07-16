<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components;

use FroshPluginUploader\Commands\DownloadPluginResourcesCommand;
use FroshPluginUploader\Components\ResourcesDownloader;
use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Components\SBP\Components\Producer;
use FroshPluginUploader\Structs\Image;
use FroshPluginUploader\Structs\Plugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResourcesDownloaderTest extends TestCase
{
    public function testDownload(): void
    {
        $folder = sys_get_temp_dir() . '/' . uniqid('test', true);

        $argvInput = new ArrayInput([
            'name' => 'Test',
            'path' => $folder,
        ]);

        $command = new DownloadPluginResourcesCommand();
        $container = new ContainerBuilder();
        $container->set(ResourcesDownloader::class, new ResourcesDownloader($this->makeClient()));
        $command->setContainer($container);
        $command->run($argvInput, new NullOutput());

        static::assertFileExists($folder . '/de.html');
        static::assertFileExists($folder . '/en.html');
        static::assertFileExists($folder . '/images/');
        static::assertFileExists($folder . '/images/0.png');
        static::assertFileExists($folder . '/store.json');

        $json = json_decode(file_get_contents($folder . '/store.json'), true);
        static::assertSame([
            'storeAvailabilities' =>
                [
                    0 => 'German',
                    1 => 'International',
                ],
            'localizations' =>
                [
                    0 => 'de_DE',
                    1 => 'en_GB',
                ],
            'categories' =>
                [
                    0 => 'System',
                    1 => 'ConversionOptimierung',
                ],
            'productType' => 'extension',
            'responsive' => true,
            'standardLocale' => 'en_GB',
            'tags' =>
                [
                    'de' =>
                        [
                            0 => 'Performance',
                            1 => 'space',
                            2 => 'Thumbnail',
                        ],
                    'en' =>
                        [
                            0 => 'performance',
                            1 => 'thumbnail',
                            2 => 'space',
                        ],
                ],
            'videos' =>
                [
                    'de' => [
                        'https://youtube.de/test'
                    ]
                ],
        ], $json);
    }

    private function makeClient(): Client
    {
        $plugin = Plugin::map(json_decode(file_get_contents(dirname(__DIR__) . '/fixtures/plugin.json')));

        $producer = $this->createMock(Producer::class);
        $producer->method('getPlugin')
            ->willReturn($plugin);

        $image = new Image();
        $image->remoteLink = 'http://placekitten.com/960/480';

        $pluginComponent = $this->createMock(\FroshPluginUploader\Components\SBP\Components\Plugin::class);
        $pluginComponent->method('getImages')
            ->willReturn([$image]);

        $client = $this->createMock(Client::class);
        $client->method('Producer')->willReturn($producer);
        $client->method('Plugins')->willReturn($pluginComponent);

        return $client;
    }
}