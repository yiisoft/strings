<?php


namespace Yiisoft\Strings;


final class NumericHelper
{

    /**
     * Converts number to its ordinal English form. For example, converts 13 to 13th, 2 to 2nd ...
     * @param int $number The number to get its ordinal value.
     * @return string
     */
    public static function toOrdinal(int $number): ?string
    {
        if (\in_array($number % 100, range(11, 13), true)) {
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
}
