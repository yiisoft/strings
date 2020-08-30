<?php

namespace Yiisoft\Strings\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Strings\NumericHelper;

final class NumericHelperTest extends TestCase
{
    public function testToOrdinal(): void
    {
        $this->assertEquals('21st', NumericHelper::toOrdinal(21));
        $this->assertEquals('22nd', NumericHelper::toOrdinal(22));
        $this->assertEquals('23rd', NumericHelper::toOrdinal(23));
        $this->assertEquals('24th', NumericHelper::toOrdinal(24));
        $this->assertEquals('25th', NumericHelper::toOrdinal(25));
        $this->assertEquals('111th', NumericHelper::toOrdinal(111));
        $this->assertEquals('113th', NumericHelper::toOrdinal(113));
    }

    public function testFloatToString(): void
    {
        $this->assertSame('10.111', NumericHelper::floatToString('10,111'));
    }

    public function testNormalizeNumber(): void
    {
        $setLocale = setlocale(LC_ALL, 'Norwegian');

        if (!$setLocale) {
            $this->markTestSkipped('Norwegian locale not found.');
        }

        $this->assertSame('10.000', NumericHelper::normalizeNumber('10,000'));
    }
}
