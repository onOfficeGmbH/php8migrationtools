<?php

use onOffice\Migration\Php8\DropIns\Phase1;
use onOffice\Migration\Php8\DropIns\Phase2;

if (7 === PHP_MAJOR_VERSION) {
    /**
     * @param mixed $needle
     * @param bool|null $strict
     */
    function c_inArray($needle, array $haystack, $strict = null): bool
    {
        return Phase1::inArray($needle, $haystack, $strict);
    }
} else {
    /**
     * @param mixed $needle
     * @param bool|null $strict
     */
    function c_inArray($needle, array $haystack, $strict = null): bool
    {
        return Phase2::inArray($needle, $haystack, $strict);
    }
}
