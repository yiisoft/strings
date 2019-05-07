<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Strings\Tests;

use PHPUnit\Framework\Constraint\Constraint;
use yii\helpers\VarDumper;

/**
 * IsOneOfAssert asserts that the value is one of the expected values.
 */
class IsOneOfAssert extends Constraint
{
    private $allowedValues;

    /**
     * IsOneOfAssert constructor.
     * @param array $allowedValues
     */
    public function __construct(array $allowedValues)
    {
        parent::__construct();
        $this->allowedValues = $allowedValues;
    }


    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString(): string
    {
        $allowedValues = array_map(static function ($value) {
            return VarDumper::dumpAsString($value);
        }, $this->allowedValues);
        $expectedAsString = implode(', ', $allowedValues);
        return "is one of $expectedAsString";
    }

    /**
     * {@inheritdoc}
     */
    protected function matches($other): bool
    {
        return in_array($other, $this->allowedValues, false);
    }
}
