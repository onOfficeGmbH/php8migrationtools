<?php

declare(strict_types=1);

namespace onOffice\Migration\Php8\Tests\DiSystem;

use onOffice\Migration\Php8\DiSystem\Container;
use onOffice\Migration\Php8\DropIns\Incident\GenericIncident;
use onOffice\Migration\Php8\DropIns\Incident\ShortBacktrace;
use onOffice\Migration\Php8\DropIns\Incident\TecIncident;
use onOffice\Migration\Php8\DropIns\Interfaces\DebugMode;
use onOffice\Migration\Php8\DropIns\Interfaces\Incident;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use ReflectionException;

class ContainerTest extends TestCase
{
    public function testGet_alreadyRegistered(): void
    {
        $diContainer = new Container();
        $diContainer->get(ShortBacktrace::class);
        $this->assertInstanceOf(ShortBacktrace::class, $diContainer->get(ShortBacktrace::class));
    }

    public function testGet_withInstantiable(): void
    {
        $diContainer = new Container();
        $this->assertInstanceOf(ShortBacktrace::class, $diContainer->get(ShortBacktrace::class));
    }

    public function testGet_withConfiguredInterface(): void
    {
        $localTestClass = new class () implements DebugMode {
            public function isEnabled(): bool
            {
                return true;
            }
        };

        $diContainer = new Container([DebugMode::class => get_class($localTestClass)]);
        $this->assertInstanceOf(get_class($localTestClass), $diContainer->get(DebugMode::class));
    }

    public function testGet_withConfiguredInstances(): void
    {
        $localTestClass = new class () implements DebugMode {
            public function isEnabled(): bool
            {
                return true;
            }
        };

        $diContainer = new Container([], [DebugMode::class => $localTestClass]);
        $this->assertSame($localTestClass, $diContainer->get(DebugMode::class));
    }

    public function testGet_withUnknownInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Interface not available in config: onOffice\Migration\Php8\DropIns\Interfaces\Incident');

        $diContainer = new Container();
        $diContainer->get(Incident::class);
    }

    public function testGet_self(): void
    {
        $diContainer = new Container();
        $this->assertInstanceOf(Container::class, $diContainer->get(Container::class));
    }

    public function testGet_classDoesNotExist(): void
    {
        $this->expectException(ReflectionException::class);

        $diContainer = new Container();
        $diContainer->get("NotInstantiableRandomString");
    }

    public function testGet_invalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Not instantiable: '.GenericIncident::class);

        $diContainer = new Container();
        $diContainer->get(GenericIncident::class);
    }

    public function testGetConfiguration(): void
    {
        $diContainer = new Container();
        $this->assertEquals([], $diContainer->getConfiguration());

        $localTestClass = new class () implements DebugMode {
            public function isEnabled(): bool
            {
                return true;
            }
        };

        $diContainer = new Container([DebugMode::class => get_class($localTestClass)]);
        $this->assertEquals([DebugMode::class => get_class($localTestClass)], $diContainer->getConfiguration());
        $this->assertInstanceOf(get_class($localTestClass), $diContainer->get(DebugMode::class));
    }
}
