<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\Generator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

/**
 * Class StructGenerator
 */
class StructGenerator
{
    public function __construct(string $jsonFile)
    {
        $fileName = 'Plugin';

        $phpNamespace = new PhpNamespace('FroshPluginUploader\\Structs');
        $class = $phpNamespace->addClass($fileName);
        $class->addExtend('FroshPluginUploader\\Structs\\Struct');

        $data = json_decode(file_get_contents($jsonFile), true)[0];

        $mappedFields = [];

        foreach ($data as $key => $value) {
            if ($propertyData = $this->addProperty($class, $key, $value)) {
                $mappedFields += $propertyData;
            }
        }

        $class->addProperty('mappedFields')
            ->setValue($mappedFields)
            ->setStatic()
        ;

        file_put_contents('src/Structs/' . $fileName . '.php', '<?php' . \PHP_EOL . $phpNamespace);
    }

    private function addProperty(ClassType $class, string $key, $value)
    {
        $type = gettype($value);

        if ($type === 'array') {
            if ($this->isJsonObject($value)) {
                $ns = new PhpNamespace($class->getNamespace()->getName());
                $innerClass = $ns->addClass(ucfirst($key));
                $innerClass->addExtend('FroshPluginUploader\\Structs\\Struct');

                $mappedFields = [];

                foreach ($value as $key2 => $value2) {
                    if ($propertyData = $this->addProperty($innerClass, $key2, $value2)) {
                        $mappedFields += $propertyData;
                    }
                }

                $class->addProperty('mappedFields')
                    ->setValue($mappedFields)
                    ->setStatic()
                ;

                file_put_contents('src/Structs/' . ucfirst($key) . '.php', '<?php' . \PHP_EOL . $ns);
                $type = ucfirst($key);
            } else {
                $value = $value[0];
                $ns = new PhpNamespace($class->getNamespace()->getName());
                $innerClass = $ns->addClass(ucfirst($key));
                $innerClass->addExtend('FroshPluginUploader\\Structs\\Struct');

                foreach ($value as $key2 => $value2) {
                    $this->addProperty($innerClass, $key2, $value2);
                }

                file_put_contents('src/Structs/' . ucfirst($key) . '.php', '<?php' . \PHP_EOL . $ns);
                $type = ucfirst($key) . '[]';
            }
        }

        $class->addProperty($key)
            ->addComment('@var ' . $type)
        ;

        if (!is_array($value)) {
            return null;
        }

        return [
            $key => 'FroshPluginUploader\\Structs\\' . ucfirst($key),
        ];
    }

    private function isJsonObject(array $object)
    {
        if (isset($object[0])) {
            return false;
        }

        return true;
    }
}
