<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components;

use FroshPluginUploader\Components\StoreJsonLoader;
use FroshPluginUploader\Structs\Image;
use FroshPluginUploader\Structs\Plugin;
use PHPUnit\Framework\TestCase;

class StoreJsonLoaderTest extends TestCase
{
    public function testLoadImageWithoutChanges(): void
    {
        $loader = new StoreJsonLoader(dirname(__DIR__) . '/fixtures/store.json');
        $image = Image::make([
            'details' => [
                'locale' => [
                    'name' => 'de_DE'
                ]
            ]
        ]);

        $before = json_encode($image);
        $loader->applyImageUpdate($image, 'test');
        static::assertSame($before, json_encode($image));
    }

    public function testLoadImage(): void
    {
        $loader = new StoreJsonLoader(dirname(__DIR__) . '/fixtures/store.json');
        $image = Image::make([
            'details' => [
                [
                    'locale' => [
                        'name' => 'de_DE'
                    ]
                ],
                [
                    'locale' => [
                        'name' => 'en_GB'
                    ]
                ]
            ]
        ]);

        $before = json_encode($image);
        $loader->applyImageUpdate($image, 'nice_english_image.jpg');
        static::assertNotSame($before, json_encode($image));

        static::assertSame(5, $image->priority);
        static::assertFalse($image->details[0]->activated);
        static::assertFalse($image->details[0]->preview);
    }

    public function testPlugin(): void
    {
        $loader = new StoreJsonLoader(dirname(__DIR__) . '/fixtures/store.json');
        $plugin = Plugin::make([
            'infos' => [
                [
                    'name' => 'Test',
                    'locale' => [
                        'name' => 'de_DE'
                    ]
                ],
                [
                    'name' => 'Test',
                    'locale' => [
                        'name' => 'en_GB'
                    ]
                ]
            ],
            'standardLocale' => [
                'name' => 'en_GB'
            ]
        ]);

        $allData = json_decode(file_get_contents(dirname(__DIR__) . '/fixtures/store_data.json'), true);

        $loader->applyToPlugin($plugin, $allData);
        static::assertTrue($plugin->responsive);
        static::assertSame(['thumbnail', 'performance', 'space'], array_column($plugin->infos[0]->tags, 'name'));
        static::assertSame('en_GB', $plugin->standardLocale['name']);
    }

    public function testPluginTooMuchTags(): void
    {
        $loader = new StoreJsonLoader(dirname(__DIR__) . '/fixtures/store_tags.json');
        $plugin = Plugin::make([
            'infos' => [
                [
                    'name' => 'Test',
                    'locale' => [
                        'name' => 'de_DE'
                    ]
                ],
                [
                    'name' => 'Test',
                    'locale' => [
                        'name' => 'en_GB'
                    ]
                ]
            ],
            'standardLocale' => [
                'name' => 'en_GB'
            ]
        ]);

        $allData = json_decode(file_get_contents(dirname(__DIR__) . '/fixtures/store_data.json'), true);
        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('Only 5 tags are allowed');

        $loader->applyToPlugin($plugin, $allData);
    }

    public function testPluginTooMuchCategories(): void
    {
        $loader = new StoreJsonLoader(dirname(__DIR__) . '/fixtures/store_category.json');
        $plugin = Plugin::make([
            'infos' => [
                [
                    'name' => 'Test',
                    'locale' => [
                        'name' => 'de_DE'
                    ]
                ],
                [
                    'name' => 'Test',
                    'locale' => [
                        'name' => 'en_GB'
                    ]
                ]
            ],
            'standardLocale' => [
                'name' => 'en_GB'
            ]
        ]);

        $allData = json_decode(file_get_contents(dirname(__DIR__) . '/fixtures/store_data.json'), true);
        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('Only 2 categories are allowed');

        $loader->applyToPlugin($plugin, $allData);
    }

    public function testPluginTooMuchVideos(): void
    {
        $loader = new StoreJsonLoader(dirname(__DIR__) . '/fixtures/store_videos.json');
        $plugin = Plugin::make([
            'infos' => [
                [
                    'name' => 'Test',
                    'locale' => [
                        'name' => 'de_DE'
                    ]
                ],
                [
                    'name' => 'Test',
                    'locale' => [
                        'name' => 'en_GB'
                    ]
                ]
            ],
            'standardLocale' => [
                'name' => 'en_GB'
            ]
        ]);

        $allData = json_decode(file_get_contents(dirname(__DIR__) . '/fixtures/store_data.json'), true);
        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('Only 2 videos are allowed');

        $loader->applyToPlugin($plugin, $allData);
    }
}
