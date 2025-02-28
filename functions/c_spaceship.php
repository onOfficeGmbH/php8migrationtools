<?php

use onOffice\Migration\Php8\DropIns\Phase1;
use onOffice\Migration\Php8\DropIns\Phase2;

if (7 === PHP_MAJOR_VERSION) {
    /**
     * @param mixed $a
     * @param mixed $b
     */
    function c_spaceship($a, $b): int
    {
        return Phase1::spaceship($a, $b);
    }
} else {
    /**
     * @param mixed $a
     * @param mixed $b
     */
    function c_spaceship($a, $b): int
    {
        return Phase2::spaceship($a, $b);
    }
}
