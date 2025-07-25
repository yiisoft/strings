<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests\Benchmark;

use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use Yiisoft\Strings\CombinedRegexp;
use Yiisoft\Strings\MemoizedCombinedRegexp;

/**
 * @Iterations(10)
 * @Revs(1000)
 * @Warmup(2)
 */
final class CombinedRegexpBench
{
    private CombinedRegexp $combinedRegexp;
    private MemoizedCombinedRegexp $memoizedCombinedRegexp;

    public function __construct()
    {
        $patterns = [
            '[a-z]+',
            '[0-9]+',
            '[a-zA-Z]+',
        ];
        $this->combinedRegexp = new CombinedRegexp($patterns);
        $this->memoizedCombinedRegexp = new MemoizedCombinedRegexp($this->combinedRegexp);
    }

    public function benchMatches(): void
    {
        $this->combinedRegexp->matches('string');
        $this->combinedRegexp->matches('123');
        $this->combinedRegexp->matches('String');
    }

    public function benchGetMatchingPattern(): void
    {
        $this->combinedRegexp->getMatchingPattern('string');
        $this->combinedRegexp->getMatchingPattern('123');
        $this->combinedRegexp->getMatchingPattern('String');
    }

    public function benchGetMatchingPatternPosition(): void
    {
        $this->combinedRegexp->getMatchingPatternPosition('string');
        $this->combinedRegexp->getMatchingPatternPosition('123');
        $this->combinedRegexp->getMatchingPatternPosition('String');
    }

    public function benchMemoizedMatches(): void
    {
        $this->memoizedCombinedRegexp->matches('string');
        $this->memoizedCombinedRegexp->matches('123');
        $this->memoizedCombinedRegexp->matches('String');
    }

    public function benchMemoizedGetMatchingPattern(): void
    {
        $this->memoizedCombinedRegexp->getMatchingPattern('string');
        $this->memoizedCombinedRegexp->getMatchingPattern('123');
        $this->memoizedCombinedRegexp->getMatchingPattern('String');
    }

    public function benchMemoizedGetMatchingPatternPosition(): void
    {
        $this->memoizedCombinedRegexp->getMatchingPatternPosition('string');
        $this->memoizedCombinedRegexp->getMatchingPatternPosition('123');
        $this->memoizedCombinedRegexp->getMatchingPatternPosition('String');
    }
}
