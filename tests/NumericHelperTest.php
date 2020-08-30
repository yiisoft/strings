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
}
