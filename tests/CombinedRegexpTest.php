<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Strings\CombinedRegexp;

final class CombinedRegexpTest extends TestCase
{
    ///**
    // * @dataProvider dataMatchAny
    // */
    //public function testMatchAny(array $patterns, string $string, bool $expectedResult): void
    //{
    //    $regexp = new CombinedRegexp($patterns);
    //    $actualResult = $regexp->matchAny($string);
    //    $message = sprintf(
    //        'Failed to assert that string "%s" matches the string "%s".',
    //        $regexp->getCompiledPattern(),
    //        $string
    //    );
    //    $this->assertEquals($expectedResult, $actualResult, $message);
    //}
    //
    //public static function dataMatchAny(): iterable
    //{
    //    yield 'matches the first pattern' => [
    //        [
    //            'first',
    //            'middle',
    //            'last',
    //        ],
    //        'first',
    //        true,
    //    ];
    //    yield 'matches the second pattern' => [
    //        [
    //            'first',
    //            'middle',
    //            'last',
    //        ],
    //        'middle',
    //        true,
    //    ];
    //    yield 'matches the third pattern' => [
    //        [
    //            'first',
    //            'middle',
    //            'last',
    //        ],
    //        'last',
    //        true,
    //    ];
    //    yield 'matches the word in the middle of string' => [
    //        [
    //            '^a\d$',
    //            'def',
    //        ],
    //        'the def in the middle',
    //        true,
    //    ];
    //    yield 'does not match part of regexp' => [
    //        [
    //            'first',
    //            'middle',
    //            'last',
    //        ],
    //        'rst',
    //        false,
    //    ];
    //
    //    yield 'matches a range' => [
    //        [
    //            'abc[0-9]+',
    //            'def',
    //            'ghi',
    //        ],
    //        'abc123',
    //        true,
    //    ];
    //    yield 'matches an anchor' => [
    //        [
    //            'a\d$',
    //            'def',
    //        ],
    //        'test a4',
    //        true,
    //    ];
    //    yield 'matches routes' => [
    //        [
    //            '/user/[\d+]',
    //            '/user/logout',
    //        ],
    //        '/user/123',
    //        true,
    //    ];
    //}

    /**
     * @dataProvider dataMatchPattern
     */
    public function testMatchPattern(array $patterns, string $string, string $expectedResult): void
    {
        $regexp = new CombinedRegexp($patterns);
        $actualResult = $regexp->matchPattern($string);
        $message = sprintf(
            'Failed to assert that string "%s" matches the string "%s".',
            $regexp->getCompiledPattern(),
            $string
        );
        $this->assertEquals($expectedResult, $actualResult, $message);
    }

    public static function dataMatchPattern(): iterable
    {
        yield 'matches the "first" pattern' => [
            [
                'zero',
                'first',
                'middle',
                'last',
            ],
            'first',
            'first',
        ];
        yield 'matches the regexp pattern' => [
            [
                'first',
                'def',
                '^a\d$',
            ],
            'a5',
            '^a\d$',
        ];
        yield 'matches first of two similar regexps' => [
            [
                'first',
                '/user/[\d+]',
                '/user/1',
            ],
            '/user/1',
            '/user/[\d+]',
        ];
    }
}
