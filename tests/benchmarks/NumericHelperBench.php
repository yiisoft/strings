<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests\Benchmark;

use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use Yiisoft\Strings\NumericHelper;

/**
 * @Iterations(10)
 * @Revs(1000)
 * @Warmup(2)
 */
final class NumericHelperBench
{
    public function benchToOrdinal(): void
    {
        NumericHelper::toOrdinal(1);
        NumericHelper::toOrdinal(2);
        NumericHelper::toOrdinal(3);
        NumericHelper::toOrdinal(4);
        NumericHelper::toOrdinal(11);
        NumericHelper::toOrdinal(12);
        NumericHelper::toOrdinal(13);
        NumericHelper::toOrdinal(21);
    }

    public function benchNormalize(): void
    {
        NumericHelper::normalize('1,000,000.123');
        NumericHelper::normalize('1 000 000,123');
    }
}
