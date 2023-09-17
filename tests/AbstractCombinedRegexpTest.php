<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Strings\AbstractCombinedRegexp;

abstract class AbstractCombinedRegexpTest extends TestCase
{
    /**
     * @dataProvider dataMatchAny
     */
    public function testMatchAny(array $patterns, string $string, bool $expectedResult): void
    {
        $regexp = $this->createCombinedRegexp($patterns);
        $actualResult = $regexp->matches($string);
        $message = sprintf(
            'Failed to assert that string "%s" matches the string "%s".',
            $regexp->getCompiledPattern(),
            $string
        );
        $this->assertEquals($expectedResult, $actualResult, $message);
    }

    public static function dataMatchAny(): iterable
    {
        yield 'the first pattern' => [
            [
                'first',
                'middle',
                'last',
            ],
            'first',
            true,
        ];
        yield 'the second pattern' => [
            [
                'first',
                'middle',
                'last',
            ],
            'middle',
            true,
        ];
        yield 'the third pattern' => [
            [
                'first',
                'middle',
                'last',
            ],
            'last',
            true,
        ];
        yield 'the word in the middle of string' => [
            [
                '^a\d$',
                'def',
            ],
            'the def in the middle',
            true,
        ];
        yield 'does not match part of regexp' => [
            [
                'first',
                'middle',
                'last',
            ],
            'rst',
            false,
        ];

        yield 'a range' => [
            [
                'abc[0-9]+',
                'def',
                'ghi',
            ],
            'abc123',
            true,
        ];
        yield 'an anchor' => [
            [
                'a\d$',
                'def',
            ],
            'test a4',
            true,
        ];
        yield 'routes' => [
            [
                '/user/[\d+]',
                '/user/logout',
            ],
            '/user/123',
            true,
        ];
        yield 'different quote' => [
            [
                '/user/[\d+]',
                '/user/logout',
            ],
            '/user/123',
            true,
        ];
    }

    /**
     * @dataProvider dataMatchingPattern
     */
    public function testMatchingPattern(array $patterns, string $string, string $expectedResult): void
    {
        $regexp = $this->createCombinedRegexp($patterns);
        $actualResult = $regexp->getMatchingPattern($string);
        $message = sprintf(
            'Failed to assert that string "%s" matches the string "%s".',
            $regexp->getCompiledPattern(),
            $string
        );
        $this->assertEquals($expectedResult, $actualResult, $message);
    }

    public static function dataMatchingPattern(): iterable
    {
        yield 'the "first" pattern' => [
            [
                'zero',
                'first',
                'middle',
                'last',
            ],
            'first',
            'first',
        ];
        yield 'the regexp pattern' => [
            [
                'first',
                'def',
                '^a\d$',
            ],
            'a5',
            '^a\d$',
        ];
        yield 'first of two similar regexps' => [
            [
                'first',
                '/user/[\d+]',
                '/user/1',
            ],
            '/user/1',
            '/user/[\d+]',
        ];
    }

    /**
     * @dataProvider dataMatchingPatternPosition
     */
    public function testMatchingPatternPosition(array $patterns, string $string, int $expectedResult): void
    {
        $regexp = $this->createCombinedRegexp($patterns);
        $actualResult = $regexp->getMatchingPatternPosition($string);
        $message = sprintf(
            'Failed to assert that string "%s" matches the string "%s".',
            $regexp->getCompiledPattern(),
            $string,
        );
        $this->assertEquals($expectedResult, $actualResult, $message);
    }

    public static function dataMatchingPatternPosition(): iterable
    {
        yield 'the "first" pattern' => [
            [
                'zero',
                'first',
                'middle',
                'last',
            ],
            'first',
            1,
        ];
        yield 'the regexp pattern' => [
            [
                'first',
                'def',
                '^a\d$',
            ],
            'a5',
            2,
        ];
        yield 'first of two similar regexps' => [
            [
                'first',
                '/user/[\d+]',
                '/user/1',
            ],
            '/user/1',
            1,
        ];
    }

    /**
     * @dataProvider dataMatchDifferentDelimiters
     */
    public function testMatchDifferentDelimiters(
        array $patterns,
        string $flags,
        string $string
    ): void {
        $regexp = $this->createCombinedRegexp($patterns, $flags);
        $message = sprintf(
            'Failed to assert that string "%s" matches the string "%s".',
            $regexp->getCompiledPattern(),
            $string,
        );
        $this->assertTrue($regexp->matches($string), $message);
        $this->assertEquals($patterns[0], $regexp->getMatchingPattern($string), $message);
        $this->assertEquals(0, $regexp->getMatchingPatternPosition($string), $message);
    }

    public static function dataMatchDifferentDelimiters(): iterable
    {
        yield 'ignore case' => [
            ['a\d'],
            'i',
            'A5',
        ];
        yield 'whitespace case' => [
            ['1.2'],
            's',
            "1\n2",
        ];
    }

    public function testInvalidNumberOfPatterns(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one pattern should be specified.');
        $this->createCombinedRegexp([]);
    }

    public function testInvalidMatch(): void
    {
        $regexp = $this->createCombinedRegexp(['abc']);
        $string = 'def';
        $this->assertFalse($regexp->matches($string));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to match pattern "/(?|abc)/" with string "def".');

        $regexp->getMatchingPatternPosition($string);
        $regexp->getMatchingPattern($string);
    }

    abstract protected function createCombinedRegexp(array $patterns, string $flags = ''): AbstractCombinedRegexp;
}
