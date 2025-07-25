<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests\Benchmark;

use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use Yiisoft\Strings\WildcardPattern;

/**
 * @Iterations(10)
 * @Revs(1000)
 * @Warmup(2)
 */
final class WildcardPatternBench
{
    private WildcardPattern $wildcardPattern;

    public function __construct()
    {
        $this->wildcardPattern = new WildcardPattern('*a*');
    }

    public function benchMatch(): void
    {
        $this->wildcardPattern->match('banana');
        $this->wildcardPattern->match('apple');
        $this->wildcardPattern->match('orange');
    }

    public function benchIsDynamic(): void
    {
        WildcardPattern::isDynamic('*a*');
        WildcardPattern::isDynamic('test');
    }

    public function benchQuote(): void
    {
        WildcardPattern::quote('*a*');
    }
}
