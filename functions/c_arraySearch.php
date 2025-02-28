<?php

use onOffice\Migration\Php8\DropIns\Phase1;
use onOffice\Migration\Php8\DropIns\Phase2;

if (7 === PHP_MAJOR_VERSION) {
    /**
     * @param mixed $needle
     * @param bool|null $strict
     * @return false|int|string
     */
    function c_arraySearch($needle, array $haystack, $strict = null)
    {
        return Phase1::arraySearch($needle, $haystack, $strict);
    }
} else {
    /**
     * @param mixed $needle
     * @param bool|null $strict
     * @return false|int|string
     */
    function c_arraySearch($needle, array $haystack, $strict = null)
    {
        return Phase2::arraySearch($needle, $haystack, $strict);
    }
}
