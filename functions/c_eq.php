<?php

use onOffice\Migration\Php8\DropIns\Phase1;
use onOffice\Migration\Php8\DropIns\Phase2;

if (7 === PHP_MAJOR_VERSION) {
    /**
     * @param mixed $a
     * @param mixed $b
     */
    function c_eq($a, $b): bool
    {
        return Phase1::eq($a, $b);
    }
} else {
    /**
     * @param mixed $a
     * @param mixed $b
     */
    function c_eq($a, $b): bool
    {
        return Phase2::eq($a, $b);
    }
}
