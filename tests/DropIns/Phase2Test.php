<?php

namespace onOffice\Migration\Php8\Tests\DropIns;

use onOffice\Migration\Php8\DiSystem\StaticDI;
use onOffice\Migration\Php8\DropIns\Exception\Php8MigrationException;
use onOffice\Migration\Php8\DropIns\Interfaces\DebugMode;
use onOffice\Migration\Php8\DropIns\Interfaces\Incident;
use onOffice\Migration\Php8\DropIns\Interfaces\IncidentManager;
use onOffice\Migration\Php8\DropIns\Phase2;
use PHPUnit\Framework\TestCase;

class Phase2Test extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (PHP_MAJOR_VERSION < 8) {
            $this->markTestSkipped('Phase2 requires PHP 8');
        }
    }
    public function configureContainer(bool $debugModeEnabled, array $instances = [])
    {
        $debugMode = new class ($debugModeEnabled) implements DebugMode {
            private $debugModeEnabled;
            public function __construct(bool $debugModeEnabled)
            {
                $this->debugModeEnabled = $debugModeEnabled;
            }

            public function isEnabled(): bool
            {
                return $this->debugModeEnabled;
            }
        };
        StaticDI::configure([], [DebugMode::class => $debugMode] + $instances);
    }

    public function testEq_silent_if_ok_prod()
    {
        $this->configureContainer(false);
        $this->assertTrue(Phase2::eq(0, ' 0'));
        $this->assertFalse(Phase2::eq(1, ' 0'));
    }
    public function testEq_noisy_if_fails_prod()
    {
        $incidentManager = $this->createMock(IncidentManager::class);
        $incidentManager->expects($this->once())->method('handle')->willReturnCallback(function (Incident $incident) {
            $this->assertSame('[PHP 8 migration] Phase 2: Please report to the PHP 8 migration Team', $incident->getSubject());
            $this->assertStringContainsString(' - Computed: true', $incident->getMessage());
            $this->assertStringContainsString(' - Native: false', $incident->getMessage());
            $this->assertStringContainsString(' - a: 0', $incident->getMessage());
            $this->assertStringContainsString(' - b: \'\'', $incident->getMessage());
        });

        $this->configureContainer(false, [IncidentManager::class => $incidentManager]);
        $this->assertTrue(Phase2::eq(0, ''));
    }
    public function testEq_noisy_if_fails_dev()
    {
        $this->configureContainer(true);
        $this->expectException(Php8MigrationException::class);
        $this->expectExceptionMessage('Phase 2: Please report to the PHP 8 migration Team');

        $this->assertTrue(Phase2::eq(0, ''));
    }

    public function testNe_silent_if_ok_prod()
    {
        $this->configureContainer(false);
        $this->assertTrue(Phase2::ne(1, ' 0'));
    }
    public function testNe_noisy_if_fails_prod()
    {
        $incidentManager = $this->createMock(IncidentManager::class);
        $incidentManager->expects($this->once())->method('handle')->willReturnCallback(function (Incident $incident) {
            $this->assertSame('[PHP 8 migration] Phase 2: Please report to the PHP 8 migration Team', $incident->getSubject());
            $this->assertStringContainsString(' - Computed: false', $incident->getMessage());
            $this->assertStringContainsString(' - Native: true', $incident->getMessage());
            $this->assertStringContainsString(' - a: 0', $incident->getMessage());
            $this->assertStringContainsString(' - b: \'\'', $incident->getMessage());
        });
        $this->configureContainer(false, [IncidentManager::class => $incidentManager]);
        $this->assertFalse(Phase2::ne(0, ''));
    }
    public function testNe_noisy_if_fails_dev()
    {
        $this->configureContainer(true);
        $this->expectException(Php8MigrationException::class);
        $this->expectExceptionMessage('Phase 2: Please report to the PHP 8 migration Team');

        $this->assertFalse(Phase2::ne(0, ''));
    }

    public function testLt_silent_if_ok_prod()
    {
        $this->configureContainer(false);
        $this->assertTrue(Phase2::lt('.12', ' .13'));
        $this->assertFalse(Phase2::lt('.12', ' .12'));
    }
    public function testLt_noisy_if_fails_prod()
    {
        $incidentManager = $this->createMock(IncidentManager::class);
        $incidentManager->expects($this->once())->method('handle')->willReturnCallback(function (Incident $incident) {
            $this->assertSame('[PHP 8 migration] Phase 2: Please report to the PHP 8 migration Team', $incident->getSubject());
            $this->assertStringContainsString(' - Computed: false', $incident->getMessage());
            $this->assertStringContainsString(' - Native: true', $incident->getMessage());
            $this->assertStringContainsString(' - a: \'.1d \'', $incident->getMessage());
            $this->assertStringContainsString(' - b: \' .1d \'', $incident->getMessage());
        });
        $this->configureContainer(false, [IncidentManager::class => $incidentManager]);
        $this->assertFalse(Phase2::lt('.12 ', ' .13 '));
    }
    public function testLt_noisy_if_fails_dev()
    {
        $this->configureContainer(true);
        $this->expectException(Php8MigrationException::class);
        $this->expectExceptionMessage('Phase 2: Please report to the PHP 8 migration Team');

        $this->assertFalse(Phase2::lt('.12 ', ' .13 '));
    }

    public function testLte_silent_if_ok_prod()
    {
        $this->assertTrue(Phase2::lte('.12', ' .13'));
        $this->assertTrue(Phase2::lte('.12', ' .12'));
        $this->assertFalse(Phase2::lte('.13', ' .12'));
    }
    public function testLte_noisy_if_fails_prod()
    {
        $incidentManager = $this->createMock(IncidentManager::class);
        $incidentManager->expects($this->once())->method('handle')->willReturnCallback(function (Incident $incident) {
            $this->assertSame('[PHP 8 migration] Phase 2: Please report to the PHP 8 migration Team', $incident->getSubject());
            $this->assertStringContainsString(' - Computed: false', $incident->getMessage());
            $this->assertStringContainsString(' - Native: true', $incident->getMessage());
            $this->assertStringContainsString(' - a: \'.1d \'', $incident->getMessage());
            $this->assertStringContainsString(' - b: \' .1d \'', $incident->getMessage());
        });
        $this->configureContainer(false, [IncidentManager::class => $incidentManager]);
        $this->assertFalse(Phase2::lte('.12 ', ' .12 '));
    }


    public function testLte_noisy_if_fails_dev()
    {
        $this->configureContainer(true);
        $this->expectException(Php8MigrationException::class);
        $this->expectExceptionMessage('Phase 2: Please report to the PHP 8 migration Team');

        $this->assertFalse(Phase2::lte('.12 ', ' .12 '));
    }

    public function testGt_silent_if_ok_prod()
    {
        $this->configureContainer(false);

        $this->assertTrue(Phase2::gt('1', '0'));
        $this->assertFalse(Phase2::gt('1', '1'));
        $this->assertTrue(Phase2::gt(' 2.123', '2'));
    }

    public function testGt_noisy_if_fails_prod()
    {
        $incidentManager = $this->createMock(IncidentManager::class);
        $incidentManager->expects($this->once())->method('handle')->willReturnCallback(function (Incident $incident) {
            $this->assertSame('[PHP 8 migration] Phase 2: Please report to the PHP 8 migration Team', $incident->getSubject());
            $this->assertStringContainsString(' - Computed: false', $incident->getMessage());
            $this->assertStringContainsString(' - Native: true', $incident->getMessage());
            $this->assertStringContainsString(' - a: \' d.1dd \'', $incident->getMessage());
            $this->assertStringContainsString(' - b: \'d\'', $incident->getMessage());
        });
        $this->configureContainer(false, [IncidentManager::class => $incidentManager]);
        $this->assertFalse(Phase2::gt(' 2.123 ', '2'));
    }

    public function testGt_noisy_if_fails_dev()
    {
        $this->configureContainer(true);
        $this->expectException(Php8MigrationException::class);
        $this->expectExceptionMessage('Phase 2: Please report to the PHP 8 migration Team');

        $this->assertFalse(Phase2::gt(' 2.123 ', '2'));
    }

    public function testGte_silent_if_ok_prod()
    {
        $this->configureContainer(false);
        $this->assertTrue(Phase2::gte('1', '0'));
        $this->assertTrue(Phase2::gte('1', '1'));
        $this->assertTrue(Phase2::gte(' 2.123', '2'));
        $this->assertFalse(Phase2::gte(' .9', '1'));
    }

    public function testGte_noisy_if_fails_prod()
    {
        $incidentManager = $this->createMock(IncidentManager::class);
        $incidentManager->expects($this->once())->method('handle')->willReturnCallback(function (Incident $incident) {
            $this->assertSame('[PHP 8 migration] Phase 2: Please report to the PHP 8 migration Team', $incident->getSubject());
            $this->assertStringContainsString(' - Computed: false', $incident->getMessage());
            $this->assertStringContainsString(' - Native: true', $incident->getMessage());
            $this->assertStringContainsString(' - a: \' d.1dd \'', $incident->getMessage());
            $this->assertStringContainsString(' - b: \'d.1dd\'', $incident->getMessage());
        });
        $this->configureContainer(false, [IncidentManager::class => $incidentManager]);
        $this->assertFalse(Phase2::gte(' 2.123 ', '2.123'));
    }
    public function testGte_noisy_if_fails_dev()
    {
        $this->configureContainer(true);
        $this->expectException(Php8MigrationException::class);
        $this->expectExceptionMessage('Phase 2: Please report to the PHP 8 migration Team');

        $this->assertFalse(Phase2::gte(' 2.123 ', '2.123'));
    }

    public function testSpaceship_silent_if_ok_prod()
    {
        $this->configureContainer(false);
        $this->assertSame(1, Phase2::spaceship('1', '0'));
        $this->assertSame(-1, Phase2::spaceship('0', '1'));
        $this->assertSame(0, Phase2::spaceship('0', '0'));
    }

    public function testSpaceship_noisy_if_fails_prod()
    {
        $incidentManager = $this->createMock(IncidentManager::class);
        $incidentManager->expects($this->once())->method('handle')->willReturnCallback(function (Incident $incident) {
            $this->assertSame('[PHP 8 migration] Phase 2: Please report to the PHP 8 migration Team', $incident->getSubject());
            $this->assertStringContainsString(' - Computed: -1', $incident->getMessage());
            $this->assertStringContainsString(' - Native: 1', $incident->getMessage());
            $this->assertStringContainsString(' - a: \' d.1d \'', $incident->getMessage());
            $this->assertStringContainsString(' - b: \'d\'', $incident->getMessage());
        });
        $this->configureContainer(false, [IncidentManager::class => $incidentManager]);
        $this->assertSame(-1, Phase2::spaceship(' 3.14 ', '3'));
    }
    public function testSpaceship_noisy_if_fails_dev()
    {
        $this->configureContainer(true);
        $this->expectException(Php8MigrationException::class);
        $this->expectExceptionMessage('Phase 2: Please report to the PHP 8 migration Team');

        $this->assertSame(-1, Phase2::spaceship(' 3.14 ', '3'));
    }

    public function testArrayKeys_silent_if_ok_prod()
    {
        $arrayKeys = Phase2::arrayKeys(['hello', '0', '1', ' 2 '], true);
        $this->assertSame([0, 2, 3], $arrayKeys);
    }

    public function testArrayKeys_noisy_if_fails_prod()
    {
        $incidentManager = $this->createMock(IncidentManager::class);
        $incidentManager->expects($this->once())->method('handle')->willReturnCallback(function (Incident $incident) {
            $this->assertSame('[PHP 8 migration] Phase 2: Please report to the PHP 8 migration Team', $incident->getSubject());
            $this->assertStringContainsString(' - Computed: array (
  0 => 1,
)', $incident->getMessage());
            $this->assertStringContainsString(' - Native: array (
)', $incident->getMessage());
            $this->assertStringContainsString(' - a: \'defaceValue:non-empty-array\'', $incident->getMessage());
            $this->assertStringContainsString(' - b: \'\'', $incident->getMessage());
        });
        $this->configureContainer(false, [IncidentManager::class => $incidentManager]);
        $arrayKeys = Phase2::arrayKeys(['hello', 0, '1', ' 2 '], '');
        $this->assertSame([1], $arrayKeys);
    }
    public function testArrayKeys_noisy_if_fails_dev()
    {
        $this->configureContainer(true);
        $this->expectException(Php8MigrationException::class);
        $this->expectExceptionMessage('Phase 2: Please report to the PHP 8 migration Team');

        $arrayKeys = Phase2::arrayKeys(['hello', 0, '1', ' 2 '], '');
        $this->assertSame([1], $arrayKeys);
    }

    public function testArraySearch_silent_if_ok_prod()
    {
        $this->assertSame('key2', Phase2::arraySearch('value', ['key1' => 'value1', 'key2' => 'value', 'key3' => 'value3']));
        $this->assertFalse(Phase2::arraySearch('value', ['key1' => 'value1', 'key2' => 'value2']));
        $this->assertSame(0, Phase2::arraySearch(0, [0, 1, 2, 3]));
    }

    public function testArraySearch_incident_if_strict()
    {
        $incidentManager = $this->createMock(IncidentManager::class);
        $incidentManager->expects($this->once())->method('handle')->willReturnCallback(function (Incident $incident) {
            $this->assertSame('[tec] [PHP 8 migration] Phase 2: Please report to the PHP 8 migration team: $strict was given with value true', $incident->getSubject());
        });
        $this->configureContainer(false, [IncidentManager::class => $incidentManager]);
        $this->assertSame(0, Phase2::arraySearch(0, [0, 1, 2, 3], true));
    }
    public function testArraySearch_incident_if_strict_dev()
    {
        $this->configureContainer(true);
        $this->expectException(Php8MigrationException::class);
        $this->expectExceptionMessage('Phase 2: Please report to the PHP 8 migration team: $strict was given with value true');

        $this->assertSame(0, Phase2::arraySearch(0, [0, 1, 2, 3], true));
    }

    public function testArraySearch_noisy_if_fails_prod()
    {
        $incidentManager = $this->createMock(IncidentManager::class);
        $incidentManager->expects($this->once())->method('handle')->willReturnCallback(function (Incident $incident) {
            $this->assertSame('[PHP 8 migration] Phase 2: Please report to the PHP 8 migration Team', $incident->getSubject());
            $this->assertStringContainsString(' - Computed: 0', $incident->getMessage());
            $this->assertStringContainsString(' - Native: false', $incident->getMessage());
            $this->assertStringContainsString(' - a: 0', $incident->getMessage());
            $this->assertStringContainsString(' - b: \'defaceValue:non-empty-array\'', $incident->getMessage());
        });
        $this->configureContainer(false, [IncidentManager::class => $incidentManager]);

        $this->assertSame(0, Phase2::arraySearch(0, ['', 'value2']));
    }

    public function testInArray_silent_if_ok_prod()
    {
        $this->assertFalse(Phase2::inArray('testValue', ['expectedValue', 'anotherValue']));
        $this->assertTrue(Phase2::inArray('expectedValue', ['expectedValue', 'anotherValue']));
    }

    public function testInArray_noisy_if_fails_prod()
    {
        $incidentManager = $this->createMock(IncidentManager::class);
        $incidentManager->expects($this->once())->method('handle')->willReturnCallback(function (Incident $incident) {
            $this->assertSame('[PHP 8 migration] Phase 2: Please report to the PHP 8 migration Team', $incident->getSubject());
            $this->assertStringContainsString(' - Computed: true', $incident->getMessage());
            $this->assertStringContainsString(' - Native: false', $incident->getMessage());
            $this->assertStringContainsString(' - a: 0', $incident->getMessage());
            $this->assertStringContainsString(' - b: \'defaceValue:non-empty-array\'', $incident->getMessage());
        });
        $this->configureContainer(false, [IncidentManager::class => $incidentManager]);

        $this->assertTrue(Phase2::inArray(0, ['expectedValue', 'anotherValue']));
    }
    public function testInArray_noisy_if_fails_dev()
    {
        $this->configureContainer(true);
        $this->expectException(Php8MigrationException::class);
        $this->expectExceptionMessage('Phase 2: Please report to the PHP 8 migration Team');

        $this->assertTrue(Phase2::inArray(0, ['expectedValue', 'anotherValue']));
    }

    public function tearDown(): void
    {
        StaticDI::reset();
        parent::tearDown();
    }
}
