<?php

declare(strict_types=1);

use onOffice\Migration\Php8\Rules\RevertStringComparisonArrayKeys;
use onOffice\Migration\Php8\Rules\RevertStringComparisonArraySearch;
use onOffice\Migration\Php8\Rules\RevertStringComparisonEquals;
use onOffice\Migration\Php8\Rules\RevertStringComparisonGreaterThan;
use onOffice\Migration\Php8\Rules\RevertStringComparisonGreaterThanEquals;
use onOffice\Migration\Php8\Rules\RevertStringComparisonInArray;
use onOffice\Migration\Php8\Rules\RevertStringComparisonLowerThan;
use onOffice\Migration\Php8\Rules\RevertStringComparisonLowerThanEquals;
use onOffice\Migration\Php8\Rules\RevertStringComparisonNotEquals;
use onOffice\Migration\Php8\Rules\RevertStringComparisonSpaceship;
use onOffice\Migration\Php8\Rules\RevertSwitchWithPhp7Behavior;
use onOffice\Migration\Php8\Rules\StringComparisonArrayKeys;
use onOffice\Migration\Php8\Rules\StringComparisonArraySearch;
use onOffice\Migration\Php8\Rules\StringComparisonEquals;
use onOffice\Migration\Php8\Rules\StringComparisonGreaterThan;
use onOffice\Migration\Php8\Rules\StringComparisonGreaterThanEquals;
use onOffice\Migration\Php8\Rules\StringComparisonInArray;
use onOffice\Migration\Php8\Rules\StringComparisonLowerThan;
use onOffice\Migration\Php8\Rules\StringComparisonLowerThanEquals;
use onOffice\Migration\Php8\Rules\StringComparisonNotEquals;
use onOffice\Migration\Php8\Rules\StringComparisonSpaceship;
use onOffice\Migration\Php8\Rules\SwitchWithPhp7Behavior;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths(
        [
            // Where to refactor
            __DIR__.'/../Rules/',
        ]
    );

    $rectorConfig->skip(
        [
            // files and/or directories to skip go here
            __DIR__.'/vendor/',
        ]
    );

    // this is actually important
    $rectorConfig->phpVersion(PhpVersion::PHP_72);
    $rectorConfig->disableParallel();

    // phase3 means "revert compatibility functions"
    $phase3Start = false;

    if ($phase3Start) {
        $rectorConfig->rules(
            [
                RevertSwitchWithPhp7Behavior::class, // needs to be first
                RevertStringComparisonEquals::class,
                RevertStringComparisonNotEquals::class,
                RevertStringComparisonLowerThan::class,
                RevertStringComparisonLowerThanEquals::class,
                RevertStringComparisonGreaterThan::class,
                RevertStringComparisonGreaterThanEquals::class,
                RevertStringComparisonSpaceship::class,
                RevertStringComparisonArrayKeys::class,
                RevertStringComparisonArraySearch::class,
                RevertStringComparisonInArray::class,
            ]
        );
    } else {
        $rectorConfig->rules(
            [
                StringComparisonEquals::class,
                StringComparisonNotEquals::class,
                StringComparisonLowerThan::class,
                StringComparisonLowerThanEquals::class,
                StringComparisonGreaterThan::class,
                StringComparisonGreaterThanEquals::class,
                StringComparisonSpaceship::class,
                StringComparisonArrayKeys::class,
                StringComparisonArraySearch::class,
                StringComparisonInArray::class,
                SwitchWithPhp7Behavior::class,
            ]
        );
    }
};
