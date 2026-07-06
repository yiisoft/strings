<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests\Support;

use Stringable;

final class StringableObject implements Stringable
{
    public function __construct(
        private string $string
    ) {
    }

    public function __toString(): string
    {
        return $this->string;
    }
}
