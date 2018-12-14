<?php

namespace FroshPluginUploader\Components;

class PluginXmlReader
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * PluginXmlReader constructor.
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        $this->data = json_decode(json_encode(simplexml_load_string(file_get_contents($fileName), 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * Get the newest version informations
     */
    public function getNewestChangelog(): string
    {
        foreach ($this->processChangelog($this->data['changelog']) as $changelog) {
            if ($changelog['version'] === $this->data['version']) {
                return $changelog['text'];
            }
        }

        return null;
    }

    /**
     * @param array $changelog
     * @return array
     */
    private function processChangelog(array $changelog): array
    {
        $formattedChangelog = [];
        if (isset($changelog['@attributes'])) {
            $changelog = [$changelog];
        }
        foreach ($changelog as $item) {
            $formattedChangelog[] = [
                'version' => $item['@attributes']['version'],
                'text' => is_array($item['changes']) ? $item['changes'][0] : $item['changes'],
            ];
        }
        return $formattedChangelog;
    }
}