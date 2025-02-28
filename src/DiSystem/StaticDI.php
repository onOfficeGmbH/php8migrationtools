<?php

namespace onOffice\Migration\Php8\DiSystem;

class StaticDI
{
    /** @var Container */
    private static $containerInstance = null;

    public static function configure(array $interfaceToClassMapping, array $instances = []): void
    {
        self::$containerInstance = new Container($interfaceToClassMapping, $instances);
    }

    /**
     * @template T
     * @param class-string<T> $classname
     * @return T
     */
    public static function get(string $classname): object
    {
        if (self::$containerInstance === null) {
            self::$containerInstance = new Container();
        }
        return self::$containerInstance->get($classname);
    }

    public static function reset(): void
    {
        self::$containerInstance = null;
    }
}
