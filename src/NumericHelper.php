<?php

declare(strict_types=1);

namespace Yiisoft\Strings;

/**
 * Provides static methods to work with numeric strings.
 */
final class NumericHelper
{
    /**
     * Converts number to its ordinal English form. For example, converts 13 to 13th, 2 to 2nd etc.
     *
     * @param float|int|string $value The number to get its ordinal value.
     *
     * @return string
     */
    public static function toOrdinal($value): string
    {
        if (!is_numeric($value)) {
            $type = gettype($value);
            throw new \InvalidArgumentException("Value must be numeric. $type given.");
        }

        if (fmod((float)$value, 1) !== 0.00) {
            return (string)$value;
        }

        if (\in_array($value % 100, [11, 12, 13], true)) {
            return $value . 'th';
        }
        switch ($value % 10) {
            case 1:
                return $value . 'st';
            case 2:
                return $value . 'nd';
            case 3:
                return $value . 'rd';
            default:
                return $value . 'th';
        }
    }

    /**
     * Returns string representation of a number value without thousands separators and with dot as decimal separator.
     *
     * @param float|int|string $value
     *
     * @return string
     */
    public static function normalize($value): string
    {
        /**
         * @psalm-suppress DocblockTypeContradiction
         */
        if (!is_scalar($value)) {
            $type = gettype($value);
            throw new \InvalidArgumentException("Value must be scalar. $type given.");
        }
        $value = str_replace([' ', ','], ['', '.'], (string)$value);
        return preg_replace('/\.(?=.*\.)/', '', $value);
    }

    /**
     * Checks whether the given string is integer number
     *
     * @param string $value
     *
     * @return bool
     */
    public static function isInteger(string $value): bool
    {
        return (string)(int)$value === $value;
    }
}
