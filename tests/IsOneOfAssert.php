<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests;

use PHPUnit\Framework\Constraint\Constraint;

/**
 * IsOneOfAssert asserts that the value is one of the expected values.
 */
final class IsOneOfAssert extends Constraint
{
    private array $allowedValues;

    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    public function toString(): string
    {
        $allowedValues = array_map(static function ($value) {
            return (string)$value;
        }, $this->allowedValues);
        $expectedAsString = implode(', ', $allowedValues);
        return "is one of $expectedAsString";
    }

    protected function matches($other): bool
    {
        return \in_array($other, $this->allowedValues, false);
    }
}
