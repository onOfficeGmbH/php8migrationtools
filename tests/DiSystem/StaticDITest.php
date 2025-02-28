<?php

declare(strict_types=1);

namespace onOffice\Migration\Php8\Tests\DiSystem;

use onOffice\Migration\Php8\DiSystem\StaticDI;
use onOffice\Migration\Php8\DropIns\Incident\ShortBacktrace;
use onOffice\Migration\Php8\DropIns\Interfaces\DebugMode;
use PHPUnit\Framework\TestCase;

class StaticDITest extends TestCase
{
    /**
     * @covers \onOffice\Migration\Php8\DiSystem\StaticDI::get
     */
    public function testGet(): void
    {
        $this->assertInstanceOf(ShortBacktrace::class, StaticDI::get(ShortBacktrace::class));
    }

    /**
     * @covers \onOffice\Migration\Php8\DiSystem\StaticDI::configure
     * @covers \onOffice\Migration\Php8\DiSystem\StaticDI::reset
     */
    public function testGet_withConfiguredInstances(): void
    {
        $localDebugMode = new class () implements DebugMode {
            public function isEnabled(): bool
            {
                return true;
            }
        };

        StaticDI::configure([], [DebugMode::class => $localDebugMode]);
        $this->assertSame($localDebugMode, StaticDI::get(DebugMode::class));

        StaticDI::reset();

        $this->expectException(\InvalidArgumentException::class);
        StaticDI::get(DebugMode::class);
    }
}
