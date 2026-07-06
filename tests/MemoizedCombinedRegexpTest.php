<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests;

use Yiisoft\Strings\AbstractCombinedRegexp;
use Yiisoft\Strings\CombinedRegexp;
use Yiisoft\Strings\MemoizedCombinedRegexp;

final class MemoizedCombinedRegexpTest extends AbstractCombinedRegexpTest
{
    protected function createCombinedRegexp(array $patterns, string $flags = ''): AbstractCombinedRegexp
    {
        return new MemoizedCombinedRegexp(new CombinedRegexp($patterns, $flags));
    }

    public function testMemoization(): void
    {
        $decorated = $this->createMock(AbstractCombinedRegexp::class);
        $decorated->method('getPatterns')->willReturn([
            'test1',
            'test2',
        ]);
        $decorated->expects($this->never())->method('matches');
        $decorated->expects($this->never())->method('getMatchingPattern');
        $decorated->expects($this->once())->method('getMatchingPatternPosition')->willReturn(0);

        $combinedRegexp = new MemoizedCombinedRegexp($decorated);

        $combinedRegexp->matches('test');
        $combinedRegexp->getMatchingPattern('test');
        $combinedRegexp->getMatchingPatternPosition('test');
    }
}
