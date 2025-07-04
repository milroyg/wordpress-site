<?php

namespace Staatic\Vendor\Symfony\Component\Config\Builder;

class ClassBuilder
{
    /**
     * @var string
     */
    private $namespace;
    /**
     * @var string
     */
    private $name;
    /**
     * @var mixed[]
     */
    private $properties = [];
    /**
     * @var mixed[]
     */
    private $methods = [];
    /**
     * @var mixed[]
     */
    private $require = [];
    /**
     * @var mixed[]
     */
    private $use = [];
    /**
     * @var mixed[]
     */
    private $implements = [];
    /**
     * @var bool
     */
    private $allowExtraKeys = \false;
    public function __construct(string $namespace, string $name)
    {
        $this->namespace = $namespace;
        $this->name = ucfirst($this->camelCase($name)) . 'Config';
    }
    public function getDirectory(): string
    {
        return str_replace('\\', \DIRECTORY_SEPARATOR, $this->namespace);
    }
    public function getFilename(): string
    {
        return $this->name . '.php';
    }
    public function build(): string
    {
        $rootPath = explode(\DIRECTORY_SEPARATOR, $this->getDirectory());
        $require = '';
        foreach ($this->require as $class) {
            $path = explode(\DIRECTORY_SEPARATOR, $class->getDirectory());
            $path[] = $class->getFilename();
            foreach ($rootPath as $key => $value) {
                if ($path[$key] !== $value) {
                    break;
                }
                unset($path[$key]);
            }
            $require .= sprintf('require_once __DIR__.\DIRECTORY_SEPARATOR.\'%s\';', implode('\'.\DIRECTORY_SEPARATOR.\'', $path)) . "\n";
        }
        $use = $require ? "\n" : '';
        foreach (array_keys($this->use) as $statement) {
            $use .= sprintf('use %s;', $statement) . "\n";
        }
        $implements = [] === $this->implements ? '' : 'implements ' . implode(', ', $this->implements);
        $body = '';
        foreach ($this->properties as $property) {
            $body .= '    ' . $property->getContent() . "\n";
        }
        foreach ($this->methods as $method) {
            $lines = explode("\n", $method->getContent());
            foreach ($lines as $line) {
                $body .= ($line ? '    ' . $line : '') . "\n";
            }
        }
        $content = strtr('<?php

namespace NAMESPACE;

REQUIREUSE
/**
 * This class is automatically generated to help in creating a config.
 */
class CLASS IMPLEMENTS
{
BODY
}
', ['NAMESPACE' => $this->namespace, 'REQUIRE' => $require, 'USE' => $use, 'CLASS' => $this->getName(), 'IMPLEMENTS' => $implements, 'BODY' => $body]);
        return $content;
    }
    /**
     * @param $this $class
     */
    public function addRequire($class): void
    {
        $this->require[] = $class;
    }
    /**
     * @param string $class
     */
    public function addUse($class): void
    {
        $this->use[$class] = \true;
    }
    /**
     * @param string $interface
     */
    public function addImplements($interface): void
    {
        $this->implements[] = '\\' . ltrim($interface, '\\');
    }
    /**
     * @param string $name
     * @param string $body
     * @param mixed[] $params
     */
    public function addMethod($name, $body, $params = []): void
    {
        $this->methods[] = new Method(strtr($body, ['NAME' => $this->camelCase($name)] + $params));
    }
    /**
     * @param string $name
     * @param string|null $classType
     * @param string|null $defaultValue
     */
    public function addProperty($name, $classType = null, $defaultValue = null): Property
    {
        $property = new Property($name, '_' !== $name[0] ? $this->camelCase($name) : $name);
        if (null !== $classType) {
            $property->setType($classType);
        }
        $this->properties[] = $property;
        $defaultValue = null !== $defaultValue ? sprintf(' = %s', $defaultValue) : '';
        $property->setContent(sprintf('private $%s%s;', $property->getName(), $defaultValue));
        return $property;
    }
    public function getProperties(): array
    {
        return $this->properties;
    }
    private function camelCase(string $input): string
    {
        $output = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
        return preg_replace('#\W#', '', $output);
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getNamespace(): string
    {
        return $this->namespace;
    }
    public function getFqcn(): string
    {
        return '\\' . $this->namespace . '\\' . $this->name;
    }
    /**
     * @param bool $allowExtraKeys
     */
    public function setAllowExtraKeys($allowExtraKeys): void
    {
        $this->allowExtraKeys = $allowExtraKeys;
    }
    public function shouldAllowExtraKeys(): bool
    {
        return $this->allowExtraKeys;
    }
}
