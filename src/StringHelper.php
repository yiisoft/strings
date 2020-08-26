<?php

declare(strict_types=1);

namespace Yiisoft\Strings;

use function array_slice;
use function htmlspecialchars;
use function mb_strlen;
use function mb_strtolower;
use function mb_strtoupper;
use function mb_substr;

/**
 * Provides static methods allowing you to deal with strings more efficiently.
 */
final class StringHelper
{
    /**
     * Returns the number of bytes in the given string.
     * This method ensures the string is treated as a byte array by using `mb_strlen()`.
     * @param string|null $input The string being measured for length.
     * @return int The number of bytes in the given string.
     */
    public static function byteLength(?string $input): int
    {
        return mb_strlen((string)$input, '8bit');
    }

    /**
     * Returns the portion of string specified by the start and length parameters.
     * This method ensures the string is treated as a byte array by using `mb_substr()`.
     * @param string $input The input string. Must be one character or longer.
     * @param int $start The starting position.
     * @param int|null $length The desired portion length. If not specified or `null`, there will be
     * no limit on length i.e. the output will be until the end of the string.
     * @return string The extracted part of string, or FALSE on failure or an empty string.
     * @see http://www.php.net/manual/en/function.substr.php
     */
    public static function byteSubstr(string $input, int $start, int $length = null): string
    {
        return mb_substr($input, $start, $length ?? mb_strlen($input, '8bit'), '8bit');
    }

    /**
     * Returns the trailing name component of a path.
     * This method is similar to the php function `basename()` except that it will
     * treat both \ and / as directory separators, independent of the operating system.
     * This method was mainly created to work on php namespaces. When working with real
     * file paths, PHP's `basename()` should work fine for you.
     * Note: this method is not aware of the actual filesystem, or path components such as "..".
     *
     * @param string $path A path string.
     * @param string $suffix If the name component ends in suffix this will also be cut off.
     * @return string The trailing name component of the given path.
     * @see http://www.php.net/manual/en/function.basename.php
     */
    public static function basename(string $path, string $suffix = ''): string
    {
        if (($len = mb_strlen($suffix)) > 0 && mb_substr($path, -$len) === $suffix) {
            $path = mb_substr($path, 0, -$len);
        }
        $path = rtrim(str_replace('\\', '/', $path), '/\\');
        if (($pos = mb_strrpos($path, '/')) !== false) {
            return mb_substr($path, $pos + 1);
        }

        return $path;
    }

    /**
     * Returns parent directory's path.
     * This method is similar to `dirname()` except that it will treat
     * both \ and / as directory separators, independent of the operating system.
     *
     * @param string $path A path string.
     * @return string The parent directory's path.
     * @see http://www.php.net/manual/en/function.basename.php
     */
    public static function dirname(string $path): string
    {
        $pos = mb_strrpos(str_replace('\\', '/', $path), '/');
        if ($pos !== false) {
            return mb_substr($path, 0, $pos);
        }

        return '';
    }

    /**
     * Truncates a string to the number of characters specified.
     *
     * @param string $input The string to truncate.
     * @param int $length How many characters from original string to include into truncated string.
     * @param string $suffix String to append to the end of truncated string.
     * @param string|null $encoding The charset to use, defaults to charset currently used by application.
     * @return string The truncated string.
     */
    public static function truncateCharacters(string $input, int $length, string $suffix = '…', string $encoding = null): string
    {
        if (static::strlen($input, $encoding) > $length) {
            return rtrim(static::substr($input, 0, $length, $encoding)) . $suffix;
        }

        return $input;
    }

    /**
     * Truncates a string to the number of words specified.
     *
     * @param string $input The string to truncate.
     * @param int $count How many words from original string to include into truncated string.
     * @param string $suffix String to append to the end of truncated string.
     * @return string The truncated string.
     */
    public static function truncateWords(string $input, int $count, string $suffix = '…'): string
    {
        $words = preg_split('/(\s+)/u', trim($input), -1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($words) / 2 > $count) {
            return implode('', array_slice($words, 0, ($count * 2) - 1)) . $suffix;
        }

        return $input;
    }

    /**
     * Truncate the string from the beginning.
     *
     * @param string $input String to process.
     * @param int $length Total of character to truncate.
     * @param string $suffix String to append to the beginning.
     * @return string
     */
    public static function truncateBegin(string $input, int $length, string $suffix = '…'): string
    {
        return substr_replace($input, $suffix, 0, $length);
    }

    /**
     * Truncates a string in the middle. Keeping start and end.
     * `StringHelper::truncateMiddle('Hello world number 2', 8)` produces "Hell...er 2".
     *
     * This method does not support HTML. It will strip all tags even if length is smaller than the string including tags.
     *
     * @param string $input The string to truncate.
     * @param int $length How many characters from original string to include into truncated string.
     * @param string $separator String to append in the middle of truncated string.
     * @param string $encoding The charset to use, defaults to charset currently used by application.
     * @return string The truncated string.
     */
    public static function truncateMiddle(string $input, int $length, string $separator = '...', string $encoding = 'UTF-8'): string
    {
        $strLen = mb_strlen($input, $encoding);

        if ($strLen <= $length) {
            return $input;
        }

        $partLen = (int)(floor($length / 2));
        $left = ltrim(mb_substr($input, 0, $partLen, $encoding));
        $right = rtrim(mb_substr($input, -$partLen, $partLen, $encoding));

        return $left . $separator . $right;
    }

    /**
     * Check if given string starts with specified substring.
     * Binary and multibyte safe.
     *
     * @param string $input Input string.
     * @param string|null $with Part to search inside the $string.
     * @param bool $caseSensitive Case sensitive search. Default is true. When case sensitive is enabled, $with must exactly match the starting of the string in order to get a true value.
     * @return bool Returns true if first input starts with second input, false otherwise.
     */
    public static function startsWith(string $input, ?string $with, bool $caseSensitive = true): bool
    {
        if (!$bytes = static::byteLength($with)) {
            return true;
        }
        if ($caseSensitive) {
            return strncmp($input, $with, $bytes) === 0;
        }

        return static::strtolower(static::substr($input, 0, $bytes, '8bit')) === static::strtolower($with);
    }

    /**
     * Check if given string ends with specified substring.
     * Binary and multibyte safe.
     *
     * @param string $input Input string to check.
     * @param string|null $with Part to search inside of the $string.
     * @param bool $caseSensitive Case sensitive search. Default is true. When case sensitive is enabled, $with must
     * exactly match the ending of the string in order to get a true value.
     * @return bool Returns true if first input ends with second input, false otherwise.
     */
    public static function endsWith(string $input, ?string $with, bool $caseSensitive = true): bool
    {
        if (!$bytes = static::byteLength($with)) {
            return true;
        }
        if ($caseSensitive) {
            // Warning check, see http://php.net/manual/en/function.substr-compare.php#refsect1-function.substr-compare-returnvalues
            if (static::byteLength($input) < $bytes) {
                return false;
            }

            return substr_compare($input, $with, -$bytes, $bytes) === 0;
        }

        return static::strtolower(mb_substr($input, -$bytes, mb_strlen($input, '8bit'), '8bit')) === static::strtolower($with);
    }

    /**
     * Explodes string into array, optionally trims values and skips empty ones.
     *
     * @param string $input String to be exploded.
     * @param string $delimiter Delimiter. Default is ','.
     * @param mixed $trim Whether to trim each element. Can be:
     *   - boolean - to trim normally;
     *   - string - custom characters to trim. Will be passed as a second argument to `trim()` function.
     *   - callable - will be called for each value instead of trim. Takes the only argument - value.
     * @param bool $skipEmpty Whether to skip empty strings between delimiters. Default is false.
     * @return array
     */
    public static function explode(string $input, string $delimiter = ',', $trim = true, bool $skipEmpty = false): array
    {
        $result = explode($delimiter, $input);
        if ($trim !== false) {
            if ($trim === true) {
                $trim = 'trim';
            } elseif (!\is_callable($trim)) {
                $trim = static function ($v) use ($trim) {
                    return trim($v, $trim);
                };
            }
            $result = array_map($trim, $result);
        }
        if ($skipEmpty) {
            // Wrapped with array_values to make array keys sequential after empty values removing
            $result = array_values(array_filter($result, static function ($value) {
                return $value !== '';
            }));
        }

        return $result;
    }

    /**
     * Counts words in a string.
     *
     * @param string $input
     * @return int
     */
    public static function countWords(string $input): int
    {
        return count(preg_split('/\s+/u', $input, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * Returns string representation of number value with replaced commas to dots, if decimal point
     * of current locale is comma.
     * @param int|float|string $value
     * @return string
     */
    public static function normalizeNumber($value): string
    {
        $value = (string)$value;

        $localeInfo = localeconv();
        $decimalSeparator = $localeInfo['decimal_point'] ?? null;

        if ($decimalSeparator !== null && $decimalSeparator !== '.') {
            $value = str_replace($decimalSeparator, '.', $value);
        }

        return $value;
    }

    /**
     * Encodes string into "Base 64 Encoding with URL and Filename Safe Alphabet" (RFC 4648).
     *
     * > Note: Base 64 padding `=` may be at the end of the returned string.
     * > `=` is not transparent to URL encoding.
     *
     * @see https://tools.ietf.org/html/rfc4648#page-7
     * @param string $input The string to encode.
     * @return string Encoded string.
     */
    public static function base64UrlEncode(string $input): string
    {
        return strtr(base64_encode($input), '+/', '-_');
    }

    /**
     * Decodes "Base 64 Encoding with URL and Filename Safe Alphabet" (RFC 4648).
     *
     * @see https://tools.ietf.org/html/rfc4648#page-7
     * @param string $input Encoded string.
     * @return string Decoded string.
     */
    public static function base64UrlDecode(string $input): string
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * Safely casts a float to string independent of the current locale.
     *
     * The decimal separator will always be `.`.
     * @param float|int $number A floating point number or integer.
     * @return string The string representation of the number.
     */
    public static function floatToString($number): string
    {
        // . and , are the only decimal separators known in ICU data,
        // so its safe to call str_replace here
        return str_replace(',', '.', (string) $number);
    }

    /**
     * Checks if the passed string would match the given shell wildcard pattern.
     * This function emulates {@see fnmatch()}, which may be unavailable at certain environment, using PCRE.
     * @param string $pattern The shell wildcard pattern.
     * @param string $string The tested string.
     * @param array $options Options for matching. Valid options are:
     *
     * - caseSensitive: bool, whether pattern should be case sensitive. Defaults to `true`.
     * - escape: bool, whether backslash escaping is enabled. Defaults to `true`.
     * - filePath: bool, whether slashes in string only matches slashes in the given pattern. Defaults to `false`.
     *
     * @return bool Whether the string matches pattern or not.
     */
    public static function matchWildcard(string $pattern, string $string, array $options = []): bool
    {
        if ($pattern === '*' && empty($options['filePath'])) {
            return true;
        }

        $replacements = [
            '\\\\\\\\' => '\\\\',
            '\\\\\\*' => '[*]',
            '\\\\\\?' => '[?]',
            '\*' => '.*',
            '\?' => '.',
            '\[\!' => '[^',
            '\[' => '[',
            '\]' => ']',
            '\-' => '-',
        ];

        if (isset($options['escape']) && !$options['escape']) {
            unset($replacements['\\\\\\\\'], $replacements['\\\\\\*'], $replacements['\\\\\\?']);
        }

        if (!empty($options['filePath'])) {
            $replacements['\*'] = '[^/\\\\]*';
            $replacements['\?'] = '[^/\\\\]';
        }

        $pattern = strtr(preg_quote($pattern, '#'), $replacements);
        $pattern = '#^' . $pattern . '$#us';

        if (isset($options['caseSensitive']) && !$options['caseSensitive']) {
            $pattern .= 'i';
        }

        return preg_match($pattern, $string) === 1;
    }

    /**
     * This method provides a unicode-safe implementation of built-in PHP function `ucfirst()`.
     *
     * @param string $string The string to be processed.
     * @param string|null $encoding Optional, defaults to "UTF-8".
     * @return string
     * @see https://php.net/manual/en/function.ucfirst.php
     */
    public static function ucfirst(string $string, string $encoding = null): string
    {
        $firstChar = static::substr($string, 0, 1, $encoding);
        $rest = static::substr($string, 1, null, $encoding);

        return static::strtoupper($firstChar, $encoding) . $rest;
    }

    /**
     * This method provides a unicode-safe implementation of built-in PHP function `ucwords()`.
     *
     * @param string $string The string to be processed.
     * @param string|null $encoding Optional, defaults to "UTF-8".
     * @see https://php.net/manual/en/function.ucwords.php
     * @return string
     */
    public static function ucwords(string $string, string $encoding = null): string
    {
        $words = preg_split("/\s/u", $string, -1, PREG_SPLIT_NO_EMPTY);

        $ucfirst = array_map(static function ($word) use ($encoding) {
            return static::ucfirst($word, $encoding);
        }, $words);

        return implode(' ', $ucfirst);
    }

    /**
     * Get string length.
     *
     * @param string $string String to calculate length for.
     * @param string|null $encoding Optional, defaults to "UTF-8".
     * @see https://php.net/manual/en/function.mb-strlen.php
     * @return int
     */
    public static function strlen(string $string, string $encoding = null): int
    {
        return empty($encoding) ? mb_strlen($string) : mb_strlen($string, $encoding);
    }

    /**
     * Get part of string.
     *
     * @param string $string To get substring from.
     * @param int $start Character to start at.
     * @param int|null $length Number of characters to get.
     * @param string|null $encoding Optional, defaults to "UTF-8".
     * @see https://php.net/manual/en/function.mb-substr.php
     * @return string
     */
    public static function substr(string $string, int $start, int $length = null, string $encoding = null): string
    {
        return empty($encoding) ? mb_substr($string, $start, $length) : mb_substr($string, $start, $length, $encoding);
    }

    /**
     * Make a string lowercase.
     *
     * @param string $string String to process.
     * @param string|null $encoding Optional, defaults to "UTF-8".
     * @see https://php.net/manual/en/function.mb-strtolower.php
     * @return string
     */
    public static function strtolower(string $string, string $encoding = null): string
    {
        return empty($encoding) ? mb_strtolower($string) : mb_strtolower($string, $encoding);
    }

    /**
     * Make a string uppercase.
     *
     * @param string $string String to process.
     * @param string|null $encoding Optional, defaults to "UTF-8".
     * @see https://php.net/manual/en/function.mb-strtoupper.php
     * @return string
     */
    public static function strtoupper(string $string, string $encoding = null): string
    {
        return empty($encoding) ? mb_strtoupper($string) : mb_strtoupper($string, $encoding);
    }

    /**
     * Convert special characters to HTML entities.
     *
     * @param string $string String to process.
     * @param int $flags A bitmask of one or more flags.
     * @param string|null $encoding Optional, defaults to "UTF-8".
     * @param bool $doubleEncode If set to false, method will not encode existing HTML entities.
     * @return string
     *@see https://php.net/manual/en/function.htmlspecialchars.php
     */
    public static function htmlspecialchars(string $string, int $flags, string $encoding = null, bool $doubleEncode = true): string
    {
        return empty($encoding) && $doubleEncode
            ? htmlspecialchars($string, $flags)
            : htmlspecialchars($string, $flags, $encoding ?: ini_get('default_charset'), $doubleEncode);
    }
}
