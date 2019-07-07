<?php
namespace Yiisoft\Strings\Tests;

use Yiisoft\Strings\Inflector;

/**
 * Forces Inflector::slug to use PHP even if intl is available.
 */
class FallbackInflector extends Inflector
{
    /**
     * {@inheritdoc}
     */
    protected static function hasIntl(): bool
    {
        return false;
    }
}
