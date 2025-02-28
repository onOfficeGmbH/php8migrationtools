<?php

use onOffice\Migration\Php8\DropIns\Phase1;
use onOffice\Migration\Php8\DropIns\Phase2;

if (7 === PHP_MAJOR_VERSION) {
    /**
     * @param mixed $a
     * @param mixed $b
     */
    function c_gt($a, $b): bool
    {
        return Phase1::gt($a, $b);
    }
} else {
    /**
     * @param mixed $a
     * @param mixed $b
     */
    function c_gt($a, $b): bool
    {
        return Phase2::gt($a, $b);
    }
}
