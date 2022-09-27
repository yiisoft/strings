<?php

declare(strict_types=1);

namespace Yiisoft\Strings;

use InvalidArgumentException;
use function in_array;
use function is_bool;

/**
 * Provides static methods to work with numeric strings.
 */
final class NumericHelper
{
    /**
     * Converts number to its ordinal English form. For example, converts 13 to 13th, 2 to 2nd etc.
     *
     * @param float|int|string $value The number to get its ordinal value.
     */
    public static function toOrdinal(float|int|string $value): string
    {
        if (!is_numeric($value)) {
            $type = gettype($value);
            throw new InvalidArgumentException("Value must be numeric. $type given.");
        }

        if (fmod((float)$value, 1) !== 0.00) {
            return (string)$value;
        }

        if (in_array($value % 100, [11, 12, 13], true)) {
            return $value . 'th';
        }
        return match ($value % 10) {
            1 => $value . 'st',
            2 => $value . 'nd',
            3 => $value . 'rd',
            default => $value . 'th',
        };
    }

    /**
     * Returns string representation of a number value without thousands separators and with dot as decimal separator.
     *
     * @throws InvalidArgumentException if value is not scalar.
     */
    public static function normalize(bool|float|int|string $value): string
    {
        /** @psalm-suppress DocblockTypeContradiction */
        if (!is_scalar($value)) {
            $type = gettype($value);
            throw new InvalidArgumentException("Value must be scalar. $type given.");
        }

        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        } else {
            $value = (string)$value;
        }
        $value = str_replace([' ', ','], ['', '.'], $value);
        return preg_replace('/\.(?=.*\.)/', '', $value);
    }

    /**
     * Checks whether the given string is an integer number.
     */
    public static function isInteger(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}
