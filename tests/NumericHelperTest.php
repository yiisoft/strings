<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
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

    public static function dataNormalize(): array
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

    #[DataProvider('dataNormalize')]
    public function testNormalize(mixed $input, string $expected): void
    {
        $this->assertSame($expected, NumericHelper::normalize($input));
    }

    public function testNormalizeWithIncorrectType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        NumericHelper::normalize([]);
    }

    public static function dataIsInteger(): array
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

    #[DataProvider('dataIsInteger')]
    public function testIsInteger(mixed $value, bool $expected): void
    {
        $this->assertSame($expected, NumericHelper::isInteger($value));
    }

    public static function dataConvertHumanReadableSizeToBytes(): array
    {
        return [
            // Only numbers
            ['1024', 1024],
            ['9223372036854775807', 9223372036854775807],
            // Single-character postfix
            ['512K', 524288],
            ['512k', 524288],
            ['2.5k', 2560],
            ['2.5K', 2560],
            ['128M', 134217728],
            ['128m', 134217728],
            ['4.5m', 4718592],
            ['4.5M', 4718592],
            ['2G', 2147483648],
            ['2g', 2147483648],
            ['2.5g', 2684354560],
            ['2.5G', 2684354560],
            ['1.1G', 1181116006],
            ['2t', 2199023255552],
            ['2T', 2199023255552],
            ['6.5t', 7146825580544],
            ['6.5T', 7146825580544],
            ['3p', 3377699720527872],
            ['3P', 3377699720527872],
            ['3.5p', 3940649673949184],
            ['3.5P', 3940649673949184],
            // Two-character postfix
            ['2kB', 2000],
            ['2.5kB', 2500],
            ['1MB', 1000000],
            ['3.3MB', 3300000],
            ['6GB', 6000000000],
            ['7.4GB', 7400000000],
            ['4TB', 4000000000000],
            ['4.9TB', 4900000000000],
            ['7PB', 7000000000000000],
            ['7.7PB', 7700000000000000],
            // Three-character postfix
            ['512KiB', 524288],
            ['2.5KiB', 2560],
            ['128MiB', 134217728],
            ['4.5MiB', 4718592],
            ['2GiB', 2147483648],
            ['2.5GiB', 2684354560],
            ['2TiB', 2199023255552],
            ['6.5TiB', 7146825580544],
            ['3PiB', 3377699720527872],
            ['3.5PiB', 3940649673949184],
        ];
    }

    #[DataProvider('dataConvertHumanReadableSizeToBytes')]
    public function testConvertHumanReadableSizeToBytes(string $string, int $expected): void
    {
        $this->assertSame($expected, NumericHelper::convertHumanReadableSizeToBytes($string));
    }

    public static function dataConvertHumanReadableSizeToBytesWithInvalidStrings(): array
    {
        return [
            ['12cKib', 'Incorrect input string: 12cKib'],
            ['12Kcb', 'Not supported postfix \'Kcb\' in input string: 12Kcb'],
            ['1c2kB', 'Incorrect input string: 1c2kB'],
            ['12Kc', 'Not supported postfix \'Kc\' in input string: 12Kc'],
            ['1c2k', 'Incorrect input string: 1c2k'],
            ['123n', 'Not supported postfix \'n\' in input string: 123n'],
            ['k', 'Incorrect input string: k'],
            ['K', 'Incorrect input string: K'],
            ['m', 'Incorrect input string: m'],
            ['M', 'Incorrect input string: M'],
            ['', 'Incorrect input string: '],
        ];
    }

    #[DataProvider('dataConvertHumanReadableSizeToBytesWithInvalidStrings')]
    public function testConvertHumanReadableSizeToBytesWithInvalidStrings(string $string, string $message): void
    {
        $this->expectExceptionObject(new \InvalidArgumentException($message));
        NumericHelper::convertHumanReadableSizeToBytes($string);
    }

    public static function dataTrimDecimalZeros(): array
    {
        return [
            'no decimals in integer with zeros' => ['390', '390'],
            'all zeros' => ['390.000', '390'],
            'no zeros' => ['3.14', '3.14'],
            'some zeros' => ['42.010', '42.01'],
            'zeros' => ['0.0', '0'],
            'decimal only' => ['.5', '.5'],
            'decimal zero' => ['.0', '0'],
            'start with zero' => ['0.25', '0.25'],
            'negative' => ['-3.000', '-3'],

            'not numeric with int' => ['hello 42', 'hello 42'],
            'not numeric with decimals' => ['hello 3.00', 'hello 3'],
            'not numeric with decimal zero' => ['hello .0', 'hello 0'],
            'not numeric' => ['hello', 'hello'],

            // edge cases
            'null' => [null, null],
            'empty' => ['', ''],
            'spaces' => ['   ', '   '],
            'dot' => ['.', '.'],
            'dot and zero with spaces' => ['. 00', '. '],
            'decimal in front of non numeric' => ['3.00 hello', '3.00 hello'],
            'dot zero in front of non numeric' => ['hello .00', 'hello 0'],
        ];
    }

    #[DataProvider('dataTrimDecimalZeros')]
    public function testTrimDecimalZeros(?string $input, ?string $expected): void
    {
        $this->assertSame($expected, NumericHelper::trimDecimalZeros($input));
    }
}
