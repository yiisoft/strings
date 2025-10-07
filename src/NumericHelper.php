<?php

declare(strict_types=1);

namespace Yiisoft\Strings;

use InvalidArgumentException;
use Stringable;

use function filter_var;
use function fmod;
use function gettype;
use function in_array;
use function is_bool;
use function is_numeric;
use function is_scalar;
use function preg_match;
use function preg_replace;
use function str_replace;
use function substr;

/**
 * Provides static methods to work with numeric strings.
 */
final class NumericHelper
{
    /**
     * @psalm-var array<int, array<string, int>>
     */
    private const FILESYSTEM_SIZE_POSTFIXES = [
        3 => [
            'KiB' => 1024,
            'MiB' => 1048576,
            'GiB' => 1073741824,
            'TiB' => 1099511627776,
            'PiB' => 1125899906842624,
        ],
        2 => [
            'kB' => 1000,
            'MB' => 1000000,
            'GB' => 1000000000,
            'TB' => 1000000000000,
            'PB' => 1000000000000000,
        ],
        1 => [
            'k' => 1024,
            'K' => 1024,
            'm' => 1048576,
            'M' => 1048576,
            'g' => 1073741824,
            'G' => 1073741824,
            't' => 1099511627776,
            'T' => 1099511627776,
            'p' => 1125899906842624,
            'P' => 1125899906842624,
        ],
    ];

    /**
     * Converts number to its ordinal English form. For example, converts 13 to 13th, 2 to 2nd etc.
     *
     * @param float|int|string $value The number to get its ordinal value.
     */
    public static function toOrdinal(mixed $value): string
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
     * @param bool|float|int|string|Stringable $value String in `string` or `Stringable` must be valid UTF-8 string.
     *
     * @throws InvalidArgumentException if value is not scalar.
     */
    public static function normalize(mixed $value): string
    {
        /** @psalm-suppress DocblockTypeContradiction */
        if (!is_scalar($value) && !$value instanceof Stringable) {
            $type = gettype($value);
            throw new InvalidArgumentException("Value must be scalar. $type given.");
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        $value = str_replace([' ', ','], ['', '.'], (string) $value);

        /**
         * @var string We assume that `$value` is valid UTF-8 string, so `preg_replace()` never returns `false`.
         */
        return preg_replace('/\.(?=.*\.)/', '', $value);
    }

    /**
     * Checks whether the given string is an integer number.
     *
     * Require Filter PHP extension ({@see https://www.php.net/manual/intro.filter.php}).
     */
    public static function isInteger(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Converts human readable size to bytes.
     *
     * @param string $string Human readable size. Examples: `1024`, `1kB`, `1.5M`, `1GiB`. Full
     * list of supported postfixes in {@see FILESYSTEM_SIZE_POSTFIXES}.

     * Note: This parameter must be less than `8192P` on 64-bit systems and `2G` on 32-bit systems.
     *
     * @throws InvalidArgumentException when the string is invalid.
     *
     * @return int The number of bytes equivalent to the specified string.
     *
     * @see https://www.gnu.org/software/coreutils/manual/html_node/Block-size.html
     */
    public static function convertHumanReadableSizeToBytes(string $string): int
    {
        if (is_numeric($string)) {
            return (int) $string;
        }

        foreach (self::FILESYSTEM_SIZE_POSTFIXES as $postfixLength => $postfixes) {
            $postfix = substr($string, -$postfixLength);
            if ($postfix === '' || preg_match('/\\d/', $postfix) === 1) {
                continue;
            }

            $numericPart = substr($string, 0, -$postfixLength);
            if (!is_numeric($numericPart)) {
                throw new InvalidArgumentException("Incorrect input string: $string");
            }

            $postfixMultiplier = $postfixes[$postfix] ?? null;
            if ($postfixMultiplier === null) {
                throw new InvalidArgumentException("Not supported postfix '$postfix' in input string: $string");
            }

            return (int) ((float) $numericPart * $postfixMultiplier);
        }

        throw new InvalidArgumentException("Incorrect input string: $string");
    }
}
