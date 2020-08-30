<?php

namespace Yiisoft\Strings;

/**
 * Provides static methods to work with numeric strings.
 */
final class NumericHelper
{
    /**
     * Converts number to its ordinal English form. For example, converts 13 to 13th, 2 to 2nd etc.
     * @param int|float|string $number The number to get its ordinal value.
     * @return string
     */
    public static function toOrdinal($number): ?string
    {
        if (fmod($number, 1) !== 0.00) {
            return $number;
        }

        if (\in_array($number % 100, range(11, 13), false)) {
            return $number . 'th';
        }
        switch ($number % 10) {
            case 1:
                return $number . 'st';
            case 2:
                return $number . 'nd';
            case 3:
                return $number . 'rd';
            default:
                return $number . 'th';
        }
    }

    /**
     * Returns string representation of a number value without thousands separators and with dot as decimal separator.
     * @param int|float|string $value
     * @return string
     */
    public static function normalize($value): string
    {
        $value = (string)$value;
        $value = str_replace([" ", ","], ["", "."], $value);
        return preg_replace('/\.(?=.*\.)/', '', $value);
    }
}
