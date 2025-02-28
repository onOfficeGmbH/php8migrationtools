<?php

declare(strict_types=1);

namespace onOffice\Migration\Php8\DropIns;

use DateTime;
use onOffice\Migration\Php8\DiSystem\StaticDI;
use onOffice\Migration\Php8\DropIns\Exception\IncompatibleTypeException;
use onOffice\Migration\Php8\DropIns\Incident\TecIncident;
use onOffice\Migration\Php8\DropIns\Interfaces\IncidentManager;
use RuntimeException;

/**
 * This is for Incompatible in PHP 8.0: String to Number Comparison
 */
class StringToNumberComparison
{
    /**
     * @param mixed $a
     * @param mixed $b
     */
    public static function eq($a, $b): bool
    {
        return self::spaceshipInternal($a, $b) === 0;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     */
    public static function lt($a, $b): bool
    {
        return self::spaceshipInternal($a, $b) === -1;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     */
    public static function spaceship($a, $b): int
    {
        return self::spaceshipInternal($a, $b);
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @throws IncompatibleTypeException
     */
    private static function spaceshipInternal($a, $b, bool $noisy = false, int $recursionDepth = 0): int
    {
        if ($a === $b) {
            return 0;
        }

        if ($a instanceof DateTime && $b instanceof DateTime) {
            return $a <=> $b;
        }

        if (is_object($a) && is_string($b) && method_exists($a, '__toString')) {
            $a = (string)$a;
        } elseif (is_string($a) && is_object($b) && method_exists($b, '__toString')) {
            $b = (string)$b;
        }

        if ($recursionDepth === 100) {
            StaticDI::get(IncidentManager::class)->handle(
                new TecIncident('slow type unsafe comparison', __FILE__, __LINE__)
            );
        }

        if ((is_array($a) || is_object($a)) && (is_array($b) || is_object($b))) {
            $aa = (array)$a;
            $ba = (array)$b;

            if (is_object($a) && is_object($b) && get_class($a) !== get_class($b)) {
                return 1;
            }

            if (is_array($a) && is_object($b)) {
                return -1;
            }

            if (is_object($a) && is_array($b)) {
                return 1;
            }

            $countComparison = count($aa) <=> count($ba);

            if ($countComparison !== 0) {
                return $countComparison;
            }

            $keysA = array_keys($aa);
            $keysB = array_keys($ba);
            sort($keysA);
            sort($keysB);

            if ($keysA !== $keysB) {
                if ($noisy) {
                    throw new IncompatibleTypeException();
                }

                return 1;
            }

            if ($recursionDepth >= 180) {
                throw new RuntimeException('Recursion limit of 180 was reached!');
            }

            $keys = array_keys($aa);
            foreach ($keys as $k) {
                $lhs = $aa[$k] ?? null;
                $rhs = $ba[$k] ?? null;

                if ($lhs === null || $rhs === null) {
                    $result = $lhs <=> $rhs;
                    if ($result !== 0) {
                        return $result;
                    }
                }

                $propertyCheck = self::spaceshipInternal($lhs, $rhs, false, $recursionDepth + 1);
                if ($propertyCheck !== 0) {
                    return $propertyCheck;
                }
            }

            return 0;
        }

        if (is_string($a)) {
            if (is_string($b)) {
                if (rtrim($b) !== $b || rtrim($a) !== $a) {
                    $result = strcmp($a, $b);

                    if ($result < 0) {
                        return -1;
                    }

                    if ($result > 0) {
                        return 1;
                    }

                    return 0;
                }

                return $a <=> $b;
            }
            if (is_int($b)) {
                return (float)$a <=> $b;
            }
            if (is_float($b)) {
                return (float)$a <=> $b;
            }
        } elseif (is_string($b)) {
            if (is_int($a)) {
                return $a <=> (float)$b;
            }
            if (is_float($a)) {
                return $a <=> (float)$b;
            }
        }

        return $a <=> $b;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     */
    public static function gt($a, $b): bool
    {
        if (is_object($a) && is_object($b) && get_class($a) !== get_class($b)) {
            return false;
        }

        if (is_object($a) && is_string($b) && method_exists($a, '__toString')) {
            $a = (string)$a;
        } elseif (is_string($a) && is_object($b) && method_exists($b, '__toString')) {
            $b = (string)$b;
        }

        if (is_array($a) && is_array($b)) {
            try {
                return self::spaceshipInternal($a, $b, true) === 1;
            } catch (IncompatibleTypeException $e) {
                return false;
            }
        }

        if ((is_array($a) || is_object($a)) && (is_array($b) || is_object($b))) {
            return self::spaceshipInternal($a, $b) === 1;
        }

        if (is_string($a)) {
            if (is_float($b) && is_nan($b)) {
                return false;
            }

            return self::spaceshipInternal($a, $b) === 1;
        }

        if (is_string($b)) {
            if (is_float($a) && is_nan($a)) {
                return false;
            }

            return self::spaceshipInternal($a, $b) === 1;
        }

        return $a > $b;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     */
    public static function lte($a, $b): bool
    {
        return self::spaceshipInternal($a, $b) <= 0;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     */
    public static function gte($a, $b): bool
    {
        if (is_object($a) && is_float($b)) {
            if (is_nan($b)) {
                return false;
            }

            return $b <= 0.0 || !is_infinite($b);
        }

        if (is_float($a) && is_nan($a)) {
            return is_null($b) || is_bool($b);
        }

        if (is_float($b) && is_nan($b)) {
            if (is_float($a)) {
                return false;
            }

            return !is_string($a) && !is_int($a) && $a !== null && $a !== false;
        }

        if (is_object($a) && is_object($b) && get_class($a) !== get_class($b)) {
            return false;
        }

        try {
            return self::spaceshipInternal($a, $b, true) !== -1;
        } catch (IncompatibleTypeException $e) {
            return false;
        }
    }

    /**
     * @param mixed $a
     * @param mixed $b
     */
    public static function ne($a, $b): bool
    {
        return self::spaceshipInternal($a, $b) !== 0;
    }

    /**
     * @param mixed $needle
     */
    public static function inArray($needle, array $haystack, bool $strict): bool
    {
        if (true === $strict) {
            return in_array($needle, $haystack, true);
        }

        foreach ($haystack as $bladeOfGrass) {
            if (self::eq($needle, $bladeOfGrass)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $needle
     * @return int|string|false
     */
    public static function arraySearch($needle, array $haystack, bool $strict)
    {
        if (true === $strict) {
            return array_search($needle, $haystack, true);
        }

        foreach ($haystack as $key => $bladeOfGrass) {
            if (self::eq($needle, $bladeOfGrass)) {
                return $key;
            }
        }

        return false;
    }

    /**
     * @param mixed $filterValue
     */
    public static function arrayKeys(array $array, $filterValue, bool $strict): array
    {
        if (true === $strict) {
            return array_keys($array, $filterValue, true);
        }

        $keys = [];

        foreach ($array as $key => $bladeOfGrass) {
            if (self::eq($filterValue, $bladeOfGrass)) {
                $keys [] = $key;
            }
        }

        return $keys;
    }
}
