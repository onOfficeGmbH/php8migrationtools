<?php

namespace onOffice\Migration\Php8\DropIns;

use LogicException;
use onOffice\Migration\Php8\DiSystem\StaticDI;
use onOffice\Migration\Php8\DropIns\Exception\Php8MigrationException;
use onOffice\Migration\Php8\DropIns\Incident\Php8MigrationIncident;
use onOffice\Migration\Php8\DropIns\Incident\TecIncident;
use onOffice\Migration\Php8\DropIns\Interfaces\DebugMode;
use onOffice\Migration\Php8\DropIns\Interfaces\IncidentManager;

class Phase1
{
    /**
     * @param mixed $a
     * @param mixed $b
     * @throws LogicException
     */
    public static function eq($a, $b): bool
    {
        $native = ($a == $b);
        $computed = StringToNumberComparison::eq($a, $b);

        self::validate($a, $b, $computed, $native);

        return $native;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @throws LogicException
     */
    public static function ne($a, $b): bool
    {
        $native = ($a != $b);
        $computed = StringToNumberComparison::ne($a, $b);

        self::validate($a, $b, $computed, $native);

        return $native;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @throws LogicException
     */
    public static function lt($a, $b): bool
    {
        $native = ($a < $b);
        $computed = StringToNumberComparison::lt($a, $b);

        self::validate($a, $b, $computed, $native);

        return $native;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @throws LogicException
     */
    public static function lte($a, $b): bool
    {
        $native = ($a <= $b);
        $computed = StringToNumberComparison::lte($a, $b);

        self::validate($a, $b, $computed, $native);

        return $native;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @throws LogicException
     */
    public static function gt($a, $b): bool
    {
        $native = ($a > $b);
        $computed = StringToNumberComparison::gt($a, $b);

        self::validate($a, $b, $computed, $native);

        return $native;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @throws LogicException
     */
    public static function gte($a, $b): bool
    {
        $native = ($a >= $b);
        $computed = StringToNumberComparison::gte($a, $b);

        self::validate($a, $b, $computed, $native);

        return $native;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @throws LogicException
     */
    public static function spaceship($a, $b): int
    {
        $native = ($a <=> $b);
        $computed = StringToNumberComparison::spaceship($a, $b);

        self::validate($a, $b, $computed, $native);

        return $native;
    }

    /**
     * @param mixed $filterValue
     * @throws LogicException
     */
    public static function arrayKeys(array $array, $filterValue, $strict = null): array
    {
        if (null !== $strict) {
            self::reportStrict($strict);

            if (!is_bool($strict)) {
                $strict = (bool)$strict;
            }
        }

        $native = array_keys($array, $filterValue, $strict ?? false);

        if (!$strict) {
            $computed = StringToNumberComparison::arrayKeys($array, $filterValue, $strict ?? false);

            self::validate($array, $filterValue, $computed, $native);
        }

        return $native;
    }

    /**
     * @param mixed $needle
     * @return false|int|string
     * @throws LogicException
     */
    public static function arraySearch($needle, array $haystack, $strict = null)
    {
        if (null !== $strict) {
            self::reportStrict($strict);

            if (!is_bool($strict)) {
                $strict = (bool)$strict;
            }
        }

        $native = array_search($needle, $haystack, $strict ?? false);

        if (!$strict) {
            $computed = StringToNumberComparison::arraySearch($needle, $haystack, $strict ?? false);

            self::validate($needle, $haystack, $computed, $native);
        }

        return $native;
    }

    /**
     * @param mixed $needle
     * @throws LogicException
     */
    public static function inArray($needle, array $haystack, $strict = null): bool
    {
        if (null !== $strict) {
            self::reportStrict($strict);

            if (!is_bool($strict)) {
                $strict = (bool)$strict;
            }
        }

        $native = in_array($needle, $haystack, $strict ?? false);

        if (!$strict) {
            $computed = StringToNumberComparison::inArray($needle, $haystack, $strict ?? false);

            self::validate($needle, $haystack, $computed, $native);
        }

        return $native;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @param mixed $computed
     * @param mixed $native
     * @throws LogicException
     */
    private static function validate($a, $b, $computed, $native): void
    {
        if ($computed !== $native &&
            PHP_MAJOR_VERSION === 7) {
            self::report($a, $b, $computed, $native);
        }
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @param mixed $computed
     * @param mixed $native
     * @throws LogicException
     */
    private static function report($a, $b, $computed, $native): void
    {
        $subject = 'Phase 1: Please report to the PHP 8 migration Team';

        if (StaticDI::get(DebugMode::class)->isEnabled()) {
            $details =
                'types ' . gettype($a) . ' vs ' . gettype($b)
                . ', values ' . self::exportValue($a) . ' vs ' . self::exportValue($b)
                . ', computed ' . var_export($computed, true) . ' vs native ' . var_export($native, true);

            $exception = new Php8MigrationException($subject);
            $exception->setDetails($details);

            throw $exception;
        }

        StaticDI::get(IncidentManager::class)->handle(
            new Php8MigrationIncident($subject, self::defaceValue($a), self::defaceValue($b), $computed, $native)
        );
    }

    private static function exportValue($mixed): string
    {
        ob_start();

        var_dump($mixed);

        return ob_get_clean();
    }

    private static function reportStrict($strict): void
    {
        $message = 'Phase 1: Please report to the PHP 8 migration team: $strict was given with value ' . var_export($strict, true);

        if (StaticDI::get(DebugMode::class)->isEnabled()) {
            throw new Php8MigrationException($message);
        }

        StaticDI::get(IncidentManager::class)->handle(
            new TecIncident('[PHP 8 migration] ' . $message, __FILE__, __LINE__)
        );
    }

    private static function defaceValue($mixed)
    {
        if (is_string($mixed)) {
            if (is_numeric(rtrim($mixed))) {
                return preg_replace('/[2-9]/', 'd', $mixed);
            }

            // cut longer strings
            if (strlen($mixed) > 9) {
                $mixed = substr($mixed, 0, 3) . '...' . substr($mixed, -3);
            }

            // replace all characters except '.', ' ', '0', '1'
            return preg_replace('/[^. 01]/', 'x', $mixed);
        }

        if (is_array($mixed)) {
            if ([] === $mixed) {
                return 'defaceValue:empty-array';
            }

            return 'defaceValue:non-empty-array';
        }

        if (is_object($mixed)) {
            return 'defaceValue:object<' . get_class($mixed) . '>';
        }

        return $mixed;
    }
}
