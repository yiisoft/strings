<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Strings\NumericHelper;
use Yiisoft\Strings\Tests\Support\StringableObject;

final class NumericHelperTest extends TestCase
{
    public function testToOrdinal(): void
    {
        $this->assertEquals('1st', NumericHelper::toOrdinal(1));
        $this->assertEquals('2nd', NumericHelper::toOrdinal(2));
        $this->assertEquals('3rd', NumericHelper::toOrdinal(3));
        $this->assertEquals('10th', NumericHelper::toOrdinal(10));
        $this->assertEquals('11th', NumericHelper::toOrdinal(11));
        $this->assertEquals('12th', NumericHelper::toOrdinal(12));
        $this->assertEquals('13th', NumericHelper::toOrdinal(13));
        $this->assertEquals('21st', NumericHelper::toOrdinal(21));
        $this->assertEquals('22nd', NumericHelper::toOrdinal(22));
        $this->assertEquals('23rd', NumericHelper::toOrdinal(23));
        $this->assertEquals('24th', NumericHelper::toOrdinal(24));
        $this->assertEquals('25th', NumericHelper::toOrdinal(25));
        $this->assertEquals('111th', NumericHelper::toOrdinal(111));
        $this->assertEquals('113th', NumericHelper::toOrdinal(113));
        $this->assertEquals('2.01', NumericHelper::toOrdinal(2.01));

        $this->assertEquals('42nd', NumericHelper::toOrdinal('42'));
        $this->assertEquals('3.1415926', NumericHelper::toOrdinal('3.1415926'));
    }

    public function testToOrdinalWithIncorrectType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        NumericHelper::toOrdinal('bla-bla');
    }

    public function dataNormalize(): array
    {
        return [
            'French' => ['4 294 967 295,000', '4294967295.000'],
            'German' => ['4 294 967.295,000', '4294967295.000'],
            'Spanish' => ['4.294.967.295,000', '4294967295.000'],
            'English' => ['4,294,967,295.000', '4294967295.000'],
            'Smaller' => ['10,111', '10.111'],
            'Float' => [10.01, '10.01'],
            'Int' => [10, '10'],
            'True' => [true, '1'],
            'False' => [false, '0'],
            'Stringable' => [new StringableObject('7 500,25'), '7500.25'],
        ];
    }

    /**
     * @dataProvider dataNormalize
     */
    public function testNormalize(mixed $input, string $expected): void
    {
        $this->assertSame($expected, NumericHelper::normalize($input));
    }

    public function testNormalizeWithIncorrectType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        NumericHelper::normalize([]);
    }

    public function dataIsInteger(): array
    {
        return [
            [new \stdClass(), false],
            [[], false],
            ['42', true],
            ['-42', true],
            ['0', true],
            [' 7', true],
            ['-', false],
            ['hello', false],
            ['', false],
        ];
    }

    /**
     * @dataProvider dataIsInteger
     */
    public function testIsInteger(mixed $value, bool $expected): void
    {
        $this->assertSame($expected, NumericHelper::isInteger($value));
    }
}
