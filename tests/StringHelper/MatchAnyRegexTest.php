<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests\StringHelper;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Strings\StringHelper;

final class MatchAnyRegexTest extends TestCase
{
    public static function dataBase(): array
    {
        return [
            [true, 'test', ['te[sx]t', 'm(o|a)n']],
            [false, 'hello', ['te[sx]t', 'm(o|a)n']],
            [false, 'Man', ['te[sx]t', 'm(o|a)n']],
            [true, 'Man', ['te[sx]t', 'm(o|a)n'], 'i'],
        ];
    }

    #[DataProvider('dataBase')]
    public function testBase(bool $expected, string $string, array $patterns, string $flags = ''): void
    {
        $result = StringHelper::matchAnyRegex($string, $patterns, $flags);
        $this->assertSame($expected, $result);
    }

    /**
     * @testWith [true, "test"]
     *           [false, "hello"]
     */
    public function testWithoutFlags(bool $expected, string $string): void
    {
        $result = StringHelper::matchAnyRegex($string, ['te[sx]t', 'm(o|a)n']);
        $this->assertSame($expected, $result);
    }

    public function testWithoutPatterns(): void
    {
        $result = StringHelper::matchAnyRegex('test', []);
        $this->assertFalse($result);
    }
}
