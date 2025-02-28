<?php

use onOffice\Migration\Php8\DropIns\Phase1;
use onOffice\Migration\Php8\DropIns\Phase2;

if (7 === PHP_MAJOR_VERSION) {
    /**
     * @param mixed $filterValue
     * @param bool|null $strict
     */
    function c_arrayKeys(array $array, $filterValue, $strict = null): array
    {
        return Phase1::arrayKeys($array, $filterValue, $strict);
    }
} else {
    /**
     * @param mixed $filterValue
     * @param bool|null $strict
     */
    function c_arrayKeys(array $array, $filterValue, $strict = null): array
    {
        return Phase2::arrayKeys($array, $filterValue, $strict);
    }
}
