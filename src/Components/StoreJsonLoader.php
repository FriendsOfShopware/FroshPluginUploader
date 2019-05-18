<?php

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Structs\Plugin;

class StoreJsonLoader
{
    /**
     * Available in following stores
     * @var array
     */
    private $storeAvailabilities = ['German', 'International'];

    /**
     * Main language
     * @var string
     */
    private $standardLocale = 'en_GB';

    /**
     * Available in following languages
     * @var array
     */
    private $localizations = ['de_DE', 'en_GB'];

    /**
     * Is listed in following categories
     * @var array
     */
    private $categories;

    /**
     * Extension or Theme?
     * @var string
     */
    private $productType = 'extension';

    /**
     * Plugin is responsive?
     * @var bool
     */
    private $responsive = true;

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

        if (isset($data['standardLocale']) && is_string($data['standardLocale'])) {
            $this->standardLocale = $data['standardLocale'];
        }

        if (isset($data['productType']) && is_string($data['productType'])) {
            $this->productType = $data['productType'];
        }

        if (isset($data['responsive']) && is_bool($data['responsive'])) {
            $this->responsive = $data['responsive'];
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

            throw new \RuntimeException(sprintf('Unable to map field "%s" with value "%s". Allowed values are %s', $pluginField, $this->$pluginField, implode(',', array_column($data, $sourceField))));
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

                throw new \RuntimeException(sprintf('Unable to map field "%s" with value "%s". Allowed values are %s', $pluginField, $newValue, implode(',', array_column($data, $sourceField))));
            }
        }
    }
}