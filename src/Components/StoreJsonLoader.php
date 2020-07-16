<?php declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Structs\Image;
use FroshPluginUploader\Structs\Plugin;

class StoreJsonLoader
{
    /**
     * Available in following stores
     *
     * @var array
     */
    private $storeAvailabilities = ['German', 'International'];

    /**
     * Main language
     *
     * @var string
     */
    private $standardLocale = 'en_GB';

    /**
     * Available in following languages
     *
     * @var array
     */
    private $localizations = ['de_DE', 'en_GB'];

    /**
     * Is listed in following categories
     *
     * @var array
     */
    private $categories;

    /**
     * Extension or Theme?
     *
     * @var string
     */
    private $productType = 'extension';

    /**
     * Plugin is responsive?
     *
     * @var bool
     */
    private $responsive = true;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var array
     */
    private $videos;

    /**
     * @var array
     */
    private $images = [];

    public function __construct(string $path)
    {
        if (file_exists($path)) {
            $this->loadFile($path);
        }
    }

    public function applyToPlugin(Plugin $plugin, array $globalInformation)
    {
        $this->mapData($plugin, $globalInformation['locales'], 'standardLocale', 'name');
        $this->mapData($plugin, $globalInformation['storeAvailabilities'], 'storeAvailabilities', 'name');
        $this->mapData($plugin, $globalInformation['localizations'], 'localizations', 'name');
        $this->mapData($plugin, $globalInformation['productTypes'], 'productType', 'name');
        $plugin->responsive = $this->responsive;

        if ($this->categories) {
            if (count($this->categories) > 2) {
                throw new \RuntimeException('Only 2 categories are allowed');
            }

            $this->mapData($plugin, $globalInformation['categories'], 'categories', 'name');
        }

        if ($this->tags) {
            $this->assignTags($plugin);
        }

        if ($this->videos) {
            $this->assignVideos($plugin);
        }
    }

    public function applyImageUpdate(Image $image, string $imageName): bool
    {
        if (!isset($this->images[$imageName])) {
            return false;
        }

        $config = $this->images[$imageName];

        if (isset($config['priority'])) {
            $image->priority = (int) $config['priority'];
        }

        foreach ($image->details as $detail) {
            if ($detail->locale->name === 'de_DE' && isset($config['de'])) {
                if (isset($config['de']['activated'])) {
                    $detail->activated = $config['de']['activated'];
                }

                if (isset($config['de']['preview'])) {
                    $detail->preview = $config['de']['preview'];
                }
            } elseif ($detail->locale->name === 'en_GB' && isset($config['en'])) {
                if (isset($config['en']['activated'])) {
                    $detail->activated = $config['en']['activated'];
                }

                if (isset($config['en']['preview'])) {
                    $detail->preview = $config['en']['preview'];
                }
            }
        }

        return true;
    }

    private function loadFile(string $path): void
    {
        $data = json_decode(file_get_contents($path), true);

        if (isset($data['storeAvailabilities']) && is_array($data['storeAvailabilities'])) {
            $this->storeAvailabilities = $data['storeAvailabilities'];
        }

        if (isset($data['localizations']) && is_array($data['localizations'])) {
            $this->localizations = $data['localizations'];
        }

        if (isset($data['categories']) && is_array($data['categories'])) {
            $this->categories = $data['categories'];
        }

        if (isset($data['tags']) && is_array($data['tags'])) {
            $this->tags = $data['tags'];
        }

        if (isset($data['videos']) && is_array($data['videos'])) {
            $this->videos = $data['videos'];
        }

        if (isset($data['standardLocale']) && is_string($data['standardLocale'])) {
            $this->standardLocale = $data['standardLocale'];
        }

        if (isset($data['productType']) && is_string($data['productType'])) {
            $this->productType = $data['productType'];
        }

        if (isset($data['responsive']) && is_bool($data['responsive'])) {
            $this->responsive = $data['responsive'];
        }

        if (isset($data['images']) && is_array($data['images'])) {
            $this->images = $data['images'];
        }
    }

    private function mapData(Plugin $plugin, array $data, string $pluginField, string $sourceField)
    {
        if (is_string($this->$pluginField)) {
            foreach ($data as $item) {
                if ($item[$sourceField] === $this->$pluginField) {
                    $plugin->$pluginField = $item;

                    return;
                }
            }
        }

        if (is_array($this->$pluginField)) {
            $plugin->$pluginField = [];
            foreach ($this->$pluginField as $newValue) {
                $found = false;

                foreach ($data as $item) {
                    if ($item[$sourceField] === $newValue) {
                        $plugin->$pluginField[] = $item;
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    continue;
                }
            }
        }
    }

    private function assignTags(Plugin $plugin)
    {
        foreach ($this->tags as $lang => $tags) {
            if (count($tags) > 5) {
                throw new \RuntimeException('Only 5 tags are allowed');
            }

            foreach ($plugin->infos as $infoTranslation) {
                $language = substr($infoTranslation->locale->name, 0, 2);

                if ($language === $lang) {
                    $infoTranslation->tags = array_map(function (string $name) {
                        return ['name' => $name];
                    }, $tags);
                }
            }
        }
    }

    private function assignVideos(Plugin $plugin)
    {
        foreach ($this->videos as $lang => $videos) {
            if (count($videos) > 2) {
                throw new \RuntimeException('Only 2 videos are allowed');
            }

            foreach ($plugin->infos as $infoTranslation) {
                $language = substr($infoTranslation->locale->name, 0, 2);

                if ($language === $lang) {
                    $infoTranslation->videos = array_map(function (string $name) {
                        return ['url' => $name];
                    }, $videos);
                }
            }
        }
    }
}
