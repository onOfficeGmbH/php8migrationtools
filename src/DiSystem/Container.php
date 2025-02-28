<?php

namespace onOffice\Migration\Php8\DiSystem;

use InvalidArgumentException;
use ReflectionClass;

class Container
{
    private $services;
    private $interfaceToClassMapping;

    public function __construct(array $interfaceToClassMapping = [], $instances = [])
    {
        $this->interfaceToClassMapping = $interfaceToClassMapping;
        $this->services = $instances;
    }

    public function getConfiguration(): array
    {
        return $this->interfaceToClassMapping;
    }

    /**
     * @template T
     * @param class-string<T> $classname
     * @return T
     */
    public function get(string $classname): object
    {
        $reflectionClass = new ReflectionClass($classname);

        if (isset($this->services[$classname])) {
            return $this->services[$classname];
        }

        if ($classname === self::class) {
            $this->services[$classname] = $this;
            return $this->services[$classname];
        }

        if ($reflectionClass->isInstantiable()) {
            $constructor = $reflectionClass->getConstructor();
            if ($constructor === null) {
                $this->services[$classname] = $reflectionClass->newInstanceArgs();
                return $this->services[$classname];
            }

            $parameters = [];
            foreach ($constructor->getParameters() as $parameterIndex => $parameter) {
                $parameterClass = $parameter->getClass();
                if ($parameterClass === null) {
                    throw new InvalidArgumentException('Parameter Number '.$parameterIndex.' is not a classname');
                }
                $parameters[] = $this->get($parameterClass->getName());
            }

            $this->services[$classname] = $reflectionClass->newInstanceArgs($parameters);
            return $this->services[$classname];
        }

        if ($reflectionClass->isInterface()) {
            $implementationClassName = $this->interfaceToClassMapping[$classname] ?? null;
            if ($implementationClassName === null) {
                throw new InvalidArgumentException('Interface not available in config: '.$classname);
            }
            $this->services[$classname] = $this->get($implementationClassName);
            return $this->services[$classname];
        }

        throw new InvalidArgumentException('Not instantiable: '.$classname);
    }
}
