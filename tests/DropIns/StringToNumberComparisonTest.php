<?php

declare(strict_types=1);

namespace onOffice\Migration\Php8\Tests\DropIns;

use DateTime;
use DateTimeZone;
use Generator;
use onOffice\Migration\Php8\DiSystem\StaticDI;
use onOffice\Migration\Php8\DropIns\Interfaces\Incident;
use onOffice\Migration\Php8\DropIns\Interfaces\IncidentManager;
use onOffice\Migration\Php8\DropIns\StringToNumberComparison;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdclass;

class StringToNumberComparisonTest extends TestCase
{
    private const EXPECTATIONS_FILE = __DIR__ . '/StringToNumberComparisonExpectations.php';

    public function testEq(): void
    {
        $this->assertTrue(StringToNumberComparison::eq(0, null));
        $this->assertTrue(StringToNumberComparison::eq(0.0, null));

        $this->assertTrue(StringToNumberComparison::eq(null, ''));

        $this->assertTrue(StringToNumberComparison::eq(0, '0'));
        $this->assertTrue(StringToNumberComparison::eq(0.0, '0'));

        $this->assertTrue(StringToNumberComparison::eq(0, '0.0'));
        $this->assertTrue(StringToNumberComparison::eq(0.0, '0.0'));

        $this->assertTrue(StringToNumberComparison::eq(0, 'foo'));
        $this->assertTrue(StringToNumberComparison::eq(0.0, 'foo'));

        $this->assertTrue(StringToNumberComparison::eq(0, ''));
        $this->assertTrue(StringToNumberComparison::eq(0.0, ''));

        $this->assertTrue(StringToNumberComparison::eq(0, ' '));
        $this->assertTrue(StringToNumberComparison::eq(0.0, ' '));

        $this->assertTrue(StringToNumberComparison::eq(42, ' 42'));
        $this->assertTrue(StringToNumberComparison::eq(42.0, ' 42'));

        $this->assertTrue(StringToNumberComparison::eq(42, '42 '));
        $this->assertTrue(StringToNumberComparison::eq(42.0, '42 '));

        $this->assertTrue(StringToNumberComparison::eq(42, ' 42 '));
        $this->assertTrue(StringToNumberComparison::eq(42.0, ' 42 '));

        $this->assertTrue(StringToNumberComparison::eq(42, '42foo'));
        $this->assertTrue(StringToNumberComparison::eq(42.0, '42foo'));

        $this->assertTrue(StringToNumberComparison::eq(42, ' 42foo '));
        $this->assertTrue(StringToNumberComparison::eq(42.0, ' 42foo '));

        $this->assertTrue(StringToNumberComparison::eq([1], ['1']));

        $a = new stdclass();
        $a->prop = '1';
        $b = new stdClass();
        $b->prop = 1;

        $this->assertTrue(StringToNumberComparison::eq($a, $b));

        $this->assertTrue(StringToNumberComparison::eq('0e6', '0e7'));

        $this->assertTrue(StringToNumberComparison::eq('0.0', 0));
        $this->assertTrue(StringToNumberComparison::eq('0', 0));
        $this->assertTrue(StringToNumberComparison::eq(0.0, 0));
        $this->assertFalse(StringToNumberComparison::eq('0.1', 0));
        $this->assertFalse(StringToNumberComparison::eq(0, '0.1'));
    }

    public function testEq_TrailingWhitespace(): void
    {
        $this->assertFalse(StringToNumberComparison::eq('0 ', '0'));
        $this->assertFalse(StringToNumberComparison::eq('1 ', '1'));

        $this->assertFalse(StringToNumberComparison::eq('0', '0 '));
        $this->assertFalse(StringToNumberComparison::eq('1', '1 '));

        // but
        $this->assertTrue(StringToNumberComparison::eq('0 ', '0 '));
        $this->assertTrue(StringToNumberComparison::eq('1 ', '1 '));
    }

    public function testLt(): void
    {
        $this->assertFalse(StringToNumberComparison::lt(0, 'a'));
        $this->assertFalse(StringToNumberComparison::lt(1, 'a'));

        $this->assertTrue(StringToNumberComparison::lt(-1, 'a'));
        $this->assertTrue(StringToNumberComparison::lt(0, '0.10'));

        $this->assertFalse(StringToNumberComparison::lt('abc', NAN));
    }

    public function testLt_TrailingWhitespace(): void
    {
        if (PHP_MAJOR_VERSION === 7) {
            $this->assertFalse('1 ' < '1');
            $this->assertTrue('1' < '1 ');
        }

        $this->assertFalse(StringToNumberComparison::lt('1 ', '1'));
        $this->assertTrue(StringToNumberComparison::lt('1', '1 '));
    }

    public function testLte(): void
    {
        $this->assertFalse(StringToNumberComparison::lte(1, 'a'));

        $this->assertTrue(StringToNumberComparison::lte(0, 'a'));
        $this->assertTrue(StringToNumberComparison::lte(-1, 'a'));
    }

    public function testGt(): void
    {
        $this->assertFalse(StringToNumberComparison::gt(0, 'a'));
        $this->assertFalse(StringToNumberComparison::gt(-1, 'a'));

        $this->assertTrue(StringToNumberComparison::gt(1, 'a'));
        $this->assertTrue(StringToNumberComparison::gt('0.10', 0));
    }

    public function testGt_TrailingWhitespace(): void
    {
        if (PHP_MAJOR_VERSION === 7) {
            $this->assertFalse('1' > '1 ');
            $this->assertTrue('1 ' > '1');
        }

        $this->assertFalse(StringToNumberComparison::gt('1', '1 '));
        $this->assertTrue(StringToNumberComparison::gt('1 ', '1'));
    }

    public function testGte(): void
    {
        $this->assertFalse(StringToNumberComparison::gte(-1, 'a'));

        $this->assertTrue(StringToNumberComparison::gte(0, 'a'));
        $this->assertTrue(StringToNumberComparison::gte(1, 'a'));
        $this->assertTrue(StringToNumberComparison::gte(.0, null));
        $this->assertTrue(StringToNumberComparison::gte(.1, null));
        $this->assertTrue(StringToNumberComparison::gte(true, .1));
        $this->assertFalse(StringToNumberComparison::gte('abc', NAN));
        $this->assertFalse(StringToNumberComparison::gte(null, .1));
        $this->assertFalse(StringToNumberComparison::gte(false, .1));
    }

    public function testNe(): void
    {
        $this->assertTrue(StringToNumberComparison::ne('0', null));
        $this->assertTrue(StringToNumberComparison::ne('0.0', null));

        $this->assertTrue(StringToNumberComparison::ne(42, '422foo'));
        $this->assertTrue(StringToNumberComparison::ne(42.0, '422foo'));

        $this->assertTrue(StringToNumberComparison::ne('1e6', '1e7'));
    }

    public function testSpaceship(): void
    {
        $this->assertEquals(0, StringToNumberComparison::spaceship(0, 'a'));
        $this->assertEquals(1, StringToNumberComparison::spaceship('0.10', 0));
        $this->assertEquals(-1, StringToNumberComparison::spaceship(0, '0.10'));
    }

    public function testSpaceship_WithTrailingWhitespace(): void
    {
        $this->assertEquals(1, StringToNumberComparison::spaceship('1 ', '1'));
        $this->assertEquals(-1, StringToNumberComparison::spaceship('1', '1 '));
        $this->assertEquals(0, StringToNumberComparison::spaceship('1', '1'));
        $this->assertEquals(0, StringToNumberComparison::spaceship('1 ', '1 '));
    }

    public function testInArray(): void
    {
        $this->assertTrue(StringToNumberComparison::inArray(0, ['a'], false));
        $this->assertTrue(StringToNumberComparison::inArray('a', ['a'], false));
        $this->assertTrue(StringToNumberComparison::inArray('a', [0], false));
        $this->assertTrue(StringToNumberComparison::inArray(0, [0], false));
        $this->assertTrue(StringToNumberComparison::inArray(0, ['0'], false));
        $this->assertTrue(StringToNumberComparison::inArray('0', [0], false));
        $this->assertTrue(StringToNumberComparison::inArray(null, [null], false));
    }

    public function testInArray_strict(): void
    {
        $this->assertTrue(StringToNumberComparison::inArray('a', ['a'], true));
        $this->assertTrue(StringToNumberComparison::inArray(0, [0], true));
        $this->assertTrue(StringToNumberComparison::inArray(null, [null], true));

        $this->assertFalse(StringToNumberComparison::inArray(0, ['a'], true));
        $this->assertFalse(StringToNumberComparison::inArray('a', [0], true));
        $this->assertFalse(StringToNumberComparison::inArray(0, ['0'], true));
        $this->assertFalse(StringToNumberComparison::inArray('0', [0], true));
    }

    public function testArraySearch(): void
    {
        $this->assertEquals(0, StringToNumberComparison::arraySearch('a', [0], false));
    }

    public function testArraySearch_strict(): void
    {
        $this->assertTrue(false === StringToNumberComparison::arraySearch('a', [0], true));
    }

    public function testArrayKeys(): void
    {
        $this->assertEquals([0 => 0], StringToNumberComparison::arrayKeys([0, 1, 2], 'a', false));
        $this->assertEquals([0 => 0], StringToNumberComparison::arrayKeys([0, 1, 2], '0', false));

        $this->assertEquals(
            [0 => 0, 1 => 1, 2 => 2],
            StringToNumberComparison::arrayKeys([0, 0, 0], 'a', false)
        );
    }

    public function testArrayKeys_strict(): void
    {
        $this->assertEquals([], StringToNumberComparison::arrayKeys([0, 1, 2], 'a', true));
        $this->assertEquals([], StringToNumberComparison::arrayKeys([0, 1, 2], '0', true));
        $this->assertEquals([], StringToNumberComparison::arrayKeys([0, 0, 0], 'a', true));
    }

    public function testEq_recursionDoesntMatch(): void
    {
        $incidentManagerClass = $this->createMock(IncidentManager::class);
        $incidentManagerClass
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->returnCallback(function (Incident $incident) {
                $this->assertSame('[tec] slow type unsafe comparison', $incident->getSubject());
            }));

        StaticDI::configure([], [IncidentManager::class => $incidentManagerClass]);

        $innerConfig1 = new RecursionInnerValue(false);
        $outerConfig1 = new RecursionOuterValue(4, 'hi');
        $outerConfig1->setInnerObject($innerConfig1);
        $innerConfig1->setOuterValue($outerConfig1); // note the recursion

        $innerConfig2 = new RecursionInnerValue(false);
        $outerConfig2 = new RecursionOuterValue(4, 'hi');
        $outerConfig2->setInnerObject($innerConfig2);
        $innerConfig2->setOuterValue($outerConfig2); // note the recursion

        try {
            $this->assertFalse(StringToNumberComparison::eq($innerConfig1, $innerConfig2));
            $this->fail(RuntimeException::class . ' expected!');
        } catch (RuntimeException $e) {
            $this->assertEquals('Recursion limit of 180 was reached!', $e->getMessage());
        }

        try {
            $this->assertTrue(StringToNumberComparison::ne($innerConfig1, $innerConfig2));
            $this->fail(RuntimeException::class . ' expected!');
        } catch (RuntimeException $e) {
            $this->assertEquals('Recursion limit of 180 was reached!', $e->getMessage());
        }

        StaticDI::reset();
    }

    /** @noinspection PhpObjectFieldsAreOnlyWrittenInspection */
    private function generateValueCombinations(): Generator
    {
        $stdClass = new stdClass();
        $stdClass->one = '1';
        $stdClass->two = 0;

        $stdClass2 = new stdClass();
        $stdClass2->one = '1';
        $stdClass2->two = 1;

        $nestedStdClass1 = new stdClass();
        $nestedStdClass1->one = '1';
        $nestedStdClass1->two = $stdClass;

        $nestedStdClass2 = new stdClass();
        $nestedStdClass2->one = '1';
        $nestedStdClass2->two = $stdClass2;

        $recursiveStdClass = new stdClass();
        $recursiveStdClass->recursiveSelf = $recursiveStdClass;

        // If you change this, you're going to have to rebuild the expectations array!
        $cases = [
            'k.A.',
            null,
            '',
            '0',
            '0.0',
            '0.00',
            '0.000',
            '0000-00-00',
            '0000-00-00 00:00:00',
            '00',
            0,
            false,
            true,
            1,
            INF,
            NAN,
            0.0000,
            ' ',
            '  ',
            ' 0 ',
            '0 ',
            ' 0',
            ' 0.0',
            ' 0foo',
            ' 0foo ',
            'foo0',
            ' foo0',
            'foo0 ',
            ' foo0 ',
            '0foo',
            '0foo ',
            ' k.A. ',
            '0 ',
            '0.0 ',
            '0.00 ',
            '0.000 ',
            '0000-00-00 ',
            '0000-00-00 00:00:00 ',
            '00 ',
            ' 0 ',
            ' 0.0 ',
            ' 0.00 ',
            ' 0.000 ',
            ' 0000-00-00 ',
            ' 0000-00-00 00:00:00 ',
            ' 00',
            ' 0',
            ' 0.0',
            ' 0.00',
            ' 0.000',
            ' 0000-00-00',
            ' 0000-00-00 00:00:00',
            ' 00',
            '0e0',
            '1e0',
            '0e6',
            '0e7',
            ' 0e0',
            ' 1e0',
            '0e0 ',
            '1e0 ',
            ' 0e0 ',
            ' 1e0 ',
            '0e-0',
            ' 0e-0',
            '0e-0 ',
            ' 0e-0 ',
            '0e-1',
            ' 0e-1',
            '0e-1 ',
            ' 0e-1 ',
            1.0,
            .75,
            -1,
            -1.0,
            -0.1,
            -0.75,
            -2,
            -2.0,
            '1.0',
            ' 1.0',
            ' 1.0 ',
            '0.75',
            ' 0.75',
            ' 0.75 ',
            '-1',
            ' -1',
            ' -1 ',
            '-1.0',
            ' -1.0',
            '-0.1',
            ' -0.1',
            ' -0.1 ',
            '-0.75',
            ' -0.75',
            ' -0.75 ',
            '-2',
            ' -2',
            ' -2 ',
            '-2.0',
            -INF,
            'INF',
            '-INF',
            -NAN,
            '-NAN',
            [],
            ['hello'],
            ['hello'],
            ['0'],
            ['0 '],
            [' 0 '],
            [' 0'],
            [0],
            [1],
            ['1'],
            [' 1'],
            [' 1 '],
            [' 1', ' 13'],
            [' 1', ' 13 '],
            'asdf',
            'hello',
            new stdClass(),
            $stdClass,
            new StringSerializableClass(' 2 '),
            new StringSerializableClass('0'),
            new StringSerializableClass('1'),
            new StringSerializableClass(' 0.7'),
            new StringSerializableClass('0.1'),
            new StringSerializableClass(' a'),
            new StringSerializableClass(' a '),
            new StringSerializableClass(' 0.7 '),
            new StringSerializableClass('0.7 '),
            new DateTime('2023-03-14 15:32:48'),
            new DateTime('2023-03-14 15:32:48'),
            new DateTime('2023-03-14 15:57:23'),
            new DateTime('2003-12-31 01:00:00', new DateTimeZone('Europe/Berlin')),
            new DateTime('2003-12-31 00:00:00 +00:00'),
            $nestedStdClass1,
            $nestedStdClass2,
            $recursiveStdClass,
            ['a' => null, 'b' => 7],
            ['a' => null, 'b' => 123],
            ['b' => 123, 'a' => null], // same as before, just different order
        ];

        foreach ($cases as $lhs) {
            foreach ($cases as $rhs) {
                yield [$lhs, $rhs];
            }
        }
    }

    public function testCharacteristics(): void
    {
        $this->checkOrBuildExpectationsFile();
        $this->assertTrue(is_file(self::EXPECTATIONS_FILE), 'Please check FS permissions and PHP version');

        $testExpectations72 = require self::EXPECTATIONS_FILE;
        $this->assertCount(127575, $testExpectations72);
        $operators = $this->getOperators();

        $previousSerializePrecision = ini_set('serialize_precision', '12');

        try {
            foreach ($operators as [$cbComputed, $cbNative]) {
                foreach ($this->generateValueCombinations() as [$lhs, $rhs]) {
                    $identifier = $cbComputed . '(' . serialize($lhs) . ', ' . serialize($rhs) . ')';
                    $native = $testExpectations72[$identifier];
                    $computed = @$cbComputed($lhs, $rhs);
                    $readableLeft = $this->getHumanReadableRepresentation($lhs);
                    $readableRight = $this->getHumanReadableRepresentation($rhs);

                    $this->assertEquals(
                        $native,
                        $computed,
                        $cbComputed
                        . '(' . $readableLeft . ', ' . $readableRight . ')'
                    );
                }
            }
        } finally {
            ini_set('serialize_precision', $previousSerializePrecision);
        }
    }

    /**
     * @param mixed $anything
     * @return string
     */
    private function getHumanReadableRepresentation($anything): string
    {
        if (strpos(serialize($anything), ';r:') !== false) {
            ob_start();
            var_dump($anything);

            return ob_get_clean();
        }

        return var_export($anything, true);
    }

    private function checkOrBuildExpectationsFile(): void
    {
        if (is_file(self::EXPECTATIONS_FILE) || PHP_MAJOR_VERSION === 8) {
            return;
        }

        $previousSerializePrecision = ini_set('serialize_precision', '12');

        $operators = $this->getOperators();

        $testExpectations = [];
        foreach ($operators as [$cbComputed, $cbNative]) {
            foreach ($this->generateValueCombinations() as [$lhs, $rhs]) {
                $native = @$cbNative($lhs, $rhs);
                $identifier = $cbComputed
                    . '(' . serialize($lhs) . ', ' . serialize($rhs) . ')';
                $testExpectations[$identifier] = $native;
            }
        }

        // In order to write the new expectations file, delete it, then run this test again
        $this->assertNotFalse(
            file_put_contents(
                self::EXPECTATIONS_FILE,
                '<?php' . "\n\n" . 'return ' . var_export($testExpectations, true) . ';' . "\n"
            )
        );

        ini_set('serialize_precision', $previousSerializePrecision);
    }

    /**
     * @return array[]
     */
    private function getOperators(): array
    {
        return [
            [
                StringToNumberComparison::class.'::lt',
                function ($a, $b) {
                    return $a < $b;
                },
            ],
            [
                StringToNumberComparison::class.'::gt',
                function ($a, $b) {
                    return $a > $b;
                },
            ],
            [
                StringToNumberComparison::class.'::lte',
                function ($a, $b) {
                    return $a <= $b;
                },
            ],
            [
                StringToNumberComparison::class.'::gte',
                function ($a, $b) {
                    return $a >= $b;
                },
            ],
            [
                StringToNumberComparison::class.'::eq',
                function ($a, $b) {
                    return $a == $b;
                },
            ],
            [
                StringToNumberComparison::class.'::ne',
                function ($a, $b) {
                    return $a != $b;
                },
            ],
            [
                StringToNumberComparison::class.'::spaceship',
                function ($a, $b) {
                    return $a <=> $b;
                },
            ],
        ];
    }
}
