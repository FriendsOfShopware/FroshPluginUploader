<?php


namespace FroshPluginUploader\Components\Generator;


use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

/**
 * Class StructGenerator
 * @package FroshPluginUploader\Components\Generator
 */
class StructGenerator
{
    public function __construct(string $jsonFile)
    {
        $fileName = pathinfo($jsonFile, PATHINFO_BASENAME);
        $fileName = ucfirst(str_replace('.' . pathinfo($jsonFile, PATHINFO_EXTENSION), '', $fileName));

        $phpNamespace = new PhpNamespace('FroshPluginUploader\\Structs');
        $class = $phpNamespace->addClass($fileName);

        $data = json_decode(file_get_contents($jsonFile), true)[0];

        foreach ($data as $key => $value) {
            $this->addProperty($class, $key, $value);
        }

        file_put_contents('result/' . $fileName. '.php', '<?php' . PHP_EOL . $phpNamespace);
    }

    private function addProperty(ClassType $class, string $key, $value)
    {
        $type = gettype($value);

        if ($type === 'array') {
            if ($this->isJsonObject($value)) {
                $ns = new PhpNamespace($class->getNamespace()->getName());
                $innerClass = $ns->addClass(ucfirst($key));

                foreach ($value as $key2 => $value2) {
                    $this->addProperty($innerClass, $key2, $value2);
                }

                file_put_contents('result/' . ucfirst($key). '.php', '<?php' . PHP_EOL . $ns);
                $type = ucfirst($key);
            } else {
                $value = $value[0];
                $ns = new PhpNamespace($class->getNamespace()->getName());
                $innerClass = $ns->addClass(ucfirst($key));

                foreach ($value as $key2 => $value2) {
                    $this->addProperty($innerClass, $key2, $value2);
                }

                file_put_contents('result/' . ucfirst($key). '.php', '<?php' . PHP_EOL . $ns);
                $type = ucfirst($key) . '[]';
            }
        }

        $class->addComment('@property ' . $type . ' $' . $key);
    }

    private function isJsonObject(array $object)
    {
        if (isset($object[0])) {
            return false;
        }

        return true;
    }
}