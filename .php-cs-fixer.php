<?php

declare(strict_types=1);

use PhpCsFixer\Config;

return (new Config())
    ->setRules(
        [
            // Enforce the PSR-12 Standard
            '@PSR12' => true,
        ]
    );
