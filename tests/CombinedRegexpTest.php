<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests;

use Yiisoft\Strings\AbstractCombinedRegexp;
use Yiisoft\Strings\CombinedRegexp;

final class CombinedRegexpTest extends AbstractCombinedRegexpTest
{
    protected function createCombinedRegexp(array $patterns, string $flags = ''): AbstractCombinedRegexp
    {
        return new CombinedRegexp($patterns, $flags);
    }

    public function testGetFlags()
    {
        $patterns = ['/user/[\d+]', '/user/login'];

        $this->assertSame('', (new CombinedRegexp($patterns))->getFlags());
        $this->assertSame('i', (new CombinedRegexp($patterns, 'i'))->getFlags());
    }
}
