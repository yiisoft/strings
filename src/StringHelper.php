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
 * The Yii string helper provides static methods allowing you to deal with strings more efficiently.
 */
final class StringHelper
{
    /**
     * Returns the number of bytes in the given string.
     * This method ensures the string is treated as a byte array by using `mb_strlen()`.
     * @param string $string the string being measured for length
     * @return int the number of bytes in the given string.
     */
    public static function byteLength(?string $string): int
    {
        return mb_strlen($string, '8bit');
    }

    /**
     * Returns the portion of string specified by the start and length parameters.
     * This method ensures the string is treated as a byte array by using `mb_substr()`.
     * @param string $string the input string. Must be one character or longer.
     * @param int $start the starting position
     * @param int $length the desired portion length. If not specified or `null`, there will be
     * no limit on length i.e. the output will be until the end of the string.
     * @return string the extracted part of string, or FALSE on failure or an empty string.
     * @see http://www.php.net/manual/en/function.substr.php
     */
    public static function byteSubstr(string $string, int $start, int $length = null): string
    {
        return mb_substr($string, $start, $length ?? mb_strlen($string, '8bit'), '8bit');
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
     * @return string the trailing name component of the given path.
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
     * @return string the parent directory's path.
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
     * @param string $string The string to truncate.
     * @param int $length How many characters from original string to include into truncated string.
     * @param string $suffix String to append to the end of truncated string.
     * @param string $encoding The charset to use, defaults to charset currently used by application.
     * @return string the truncated string.
     */
    public static function truncateCharacters(string $string, int $length, string $suffix = '…', string $encoding = null): string
    {
        if (static::strlen($string, $encoding) > $length) {
            return rtrim(static::substr($string, 0, $length, $encoding)) . $suffix;
        }

        return $string;
    }

    /**
     * Truncates a string to the number of words specified.
     *
     * @param string $string The string to truncate.
     * @param int $count How many words from original string to include into truncated string.
     * @param string $suffix String to append to the end of truncated string.
     * @return string the truncated string.
     */
    public static function truncateWords(string $string, int $count, string $suffix = '…'): string
    {
        $words = preg_split('/(\s+)/u', trim($string), null, PREG_SPLIT_DELIM_CAPTURE);
        if (count($words) / 2 > $count) {
            return implode('', array_slice($words, 0, ($count * 2) - 1)) . $suffix;
        }

        return $string;
    }

    /**
     * Truncate the string from the beginning
     *
     * @param string $string string to process
     * @param int $length total of character to truncate
     * @param string $suffix String to append to the beginning
     * @return string
     */
    public static function truncateBegin(string $string, int $length, string $suffix = '…'): string
    {
        return substr_replace($string, $suffix, 0, $length);
    }

    /**
     * Truncates a string in the middle. Keeping start and end.
     * `StringHelper::truncateMiddle('Hello world number 2', 8)` produces "Hell...er 2".
     *
     * This method does not support HTML. It will strip all tags even if length is smaller than the string including tags.
     *
     * @param string $string The string to truncate.
     * @param int $length How many characters from original string to include into truncated string.
     * @param string $separator String to append in the middle of truncated string.
     * @param string $encoding The charset to use, defaults to charset currently used by application.
     * @return string the truncated string.
     */
    public static function truncateMiddle(string $string, int $length, string $separator = '...', string $encoding = 'UTF-8'): string
    {
        $strLen = mb_strlen($string, $encoding);

        if ($strLen <= $length) {
            return $string;
        }

        $partLen = floor($length / 2);
        $left = ltrim(mb_substr($string, 0, $partLen, $encoding));
        $right = rtrim(mb_substr($string, -$partLen, $partLen, $encoding));

        return $left . $separator . $right;
    }

    /**
     * Check if given string starts with specified substring.
     * Binary and multibyte safe.
     *
     * @param string $string Input string
     * @param string $with Part to search inside the $string
     * @param bool $caseSensitive Case sensitive search. Default is true. When case sensitive is enabled, $with must exactly match the starting of the string in order to get a true value.
     * @return bool Returns true if first input starts with second input, false otherwise
     */
    public static function startsWith(string $string, ?string $with, bool $caseSensitive = true): bool
    {
        if (!$bytes = static::byteLength($with)) {
            return true;
        }
        if ($caseSensitive) {
            return strncmp($string, $with, $bytes) === 0;
        }

        return static::strtolower(static::substr($string, 0, $bytes, '8bit')) === static::strtolower($with);
    }

    /**
     * Check if given string ends with specified substring.
     * Binary and multibyte safe.
     *
     * @param string $string Input string to check
     * @param string $with Part to search inside of the $string.
     * @param bool $caseSensitive Case sensitive search. Default is true. When case sensitive is enabled, $with must exactly match the ending of the string in order to get a true value.
     * @return bool Returns true if first input ends with second input, false otherwise
     */
    public static function endsWith(string $string, ?string $with, bool $caseSensitive = true): bool
    {
        if (!$bytes = static::byteLength($with)) {
            return true;
        }
        if ($caseSensitive) {
            // Warning check, see http://php.net/manual/en/function.substr-compare.php#refsect1-function.substr-compare-returnvalues
            if (static::byteLength($string) < $bytes) {
                return false;
            }

            return substr_compare($string, $with, -$bytes, $bytes) === 0;
        }

        return static::strtolower(mb_substr($string, -$bytes, mb_strlen($string, '8bit'), '8bit')) === static::strtolower($with);
    }

    /**
     * Explodes string into array, optionally trims values and skips empty ones.
     *
     * @param string $string String to be exploded.
     * @param string $delimiter Delimiter. Default is ','.
     * @param mixed $trim Whether to trim each element. Can be:
     *   - boolean - to trim normally;
     *   - string - custom characters to trim. Will be passed as a second argument to `trim()` function.
     *   - callable - will be called for each value instead of trim. Takes the only argument - value.
     * @param bool $skipEmpty Whether to skip empty strings between delimiters. Default is false.
     * @return array
     */
    public static function explode(string $string, string $delimiter = ',', $trim = true, bool $skipEmpty = false): array
    {
        $result = explode($delimiter, $string);
        if ($trim !== false) {
            if ($trim === true) {
                $trim = 'trim';
            } elseif (!is_callable($trim)) {
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
     * @param string $string
     * @return int
     */
    public static function countWords(string $string): int
    {
        return count(preg_split('/\s+/u', $string, null, PREG_SPLIT_NO_EMPTY));
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
     * @param string $input the string to encode.
     * @return string encoded string.
     */
    public static function base64UrlEncode(string $input): string
    {
        return strtr(base64_encode($input), '+/', '-_');
    }

    /**
     * Decodes "Base 64 Encoding with URL and Filename Safe Alphabet" (RFC 4648).
     *
     * @see https://tools.ietf.org/html/rfc4648#page-7
     * @param string $input encoded string.
     * @return string decoded string.
     */
    public static function base64UrlDecode(string $input): string
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * Safely casts a float to string independent of the current locale.
     *
     * The decimal separator will always be `.`.
     * @param float|int $number a floating point number or integer.
     * @return string the string representation of the number.
     */
    public static function floatToString($number): string
    {
        // . and , are the only decimal separators known in ICU data,
        // so its safe to call str_replace here
        return str_replace(',', '.', (string) $number);
    }

    /**
     * Checks if the passed string would match the given shell wildcard pattern.
     * This function emulates [[fnmatch()]], which may be unavailable at certain environment, using PCRE.
     * @param string $pattern the shell wildcard pattern.
     * @param string $string the tested string.
     * @param array $options options for matching. Valid options are:
     *
     * - caseSensitive: bool, whether pattern should be case sensitive. Defaults to `true`.
     * - escape: bool, whether backslash escaping is enabled. Defaults to `true`.
     * - filePath: bool, whether slashes in string only matches slashes in the given pattern. Defaults to `false`.
     *
     * @return bool whether the string matches pattern or not.
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
     * @param string $string the string to be processed
     * @param string $encoding Optional, defaults to "UTF-8"
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
     * @param string $string the string to be processed
     * @param string $encoding Optional, defaults to "UTF-8"
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
     * Get string length
     *
     * @param string $string string to calculate length for
     * @param string|null $encoding Optional, defaults to "UTF-8"
     * @see https://php.net/manual/en/function.mb-strlen.php
     * @return int
     */
    public static function strlen(string $string, string $encoding = null): int
    {
        return empty($encoding) ? mb_strlen($string) : mb_strlen($string, $encoding);
    }

    /**
     * Get part of string
     *
     * @param string $string to get substring from
     * @param int $start character to start at
     * @param int|null $length number of characters to get
     * @param string|null $encoding Optional, defaults to "UTF-8"
     * @see https://php.net/manual/en/function.mb-substr.php
     * @return string
     */
    public static function substr(string $string, int $start, int $length = null, string $encoding = null): string
    {
        return empty($encoding) ? mb_substr($string, $start, $length) : mb_substr($string, $start, $length, $encoding);
    }

    /**
     * Make a string lowercase
     *
     * @param string $string string to process
     * @param string|null $encoding Optional, defaults to "UTF-8"
     * @see https://php.net/manual/en/function.mb-strtolower.php
     * @return string
     */
    public static function strtolower(string $string, string $encoding = null): string
    {
        return empty($encoding) ? mb_strtolower($string) : mb_strtolower($string, $encoding);
    }

    /**
     * Make a string uppercase
     *
     * @param string $string string to process
     * @param string|null $encoding Optional, defaults to "UTF-8"
     * @see https://php.net/manual/en/function.mb-strtoupper.php
     * @return string
     */
    public static function strtoupper(string $string, string $encoding = null): string
    {
        return empty($encoding) ? mb_strtoupper($string) : mb_strtoupper($string, $encoding);
    }

    /**
     * Convert special characters to HTML entities
     *
     * @param string $string string to process
     * @param int $flags A bitmask of one or more flags
     * @param string|null $encoding Optional, defaults to "UTF-8"
     * @param bool $double_encode if set to false, method will not encode existing HTML entities
     * @see https://php.net/manual/en/function.htmlspecialchars.php
     * @return string
     */
    public static function htmlspecialchars(string $string, int $flags, string $encoding = null, bool $double_encode = true): string
    {
        return empty($encoding) && $double_encode
            ? htmlspecialchars($string, $flags)
            : htmlspecialchars($string, $flags, $encoding ?: ini_get('default_charset'), $double_encode);
    }
}
