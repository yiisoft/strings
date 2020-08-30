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
     * This method ensures the string is treated as a byte array even if `mbstring.func_overload` is turned on
     * by using {@see mb_strlen()}.
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
    public static function byteSubstring(string $input, int $start, int $length = null): string
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
    public static function baseName(string $path, string $suffix = ''): string
    {
        $length = mb_strlen($suffix);
        if ($length > 0 && mb_substr($path, -$length) === $suffix) {
            $path = mb_substr($path, 0, -$length);
        }
        $path = rtrim(str_replace('\\', '/', $path), '/\\');
        $position = mb_strrpos($path, '/');
        if ($position !== false) {
            return mb_substr($path, $position + 1);
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
    public static function directoryName(string $path): string
    {
        $position = mb_strrpos(str_replace('\\', '/', $path), '/');
        if ($position !== false) {
            return mb_substr($path, 0, $position);
        }

        return '';
    }

    /**
     * Check if given string starts with specified substring.
     * Binary and multibyte safe.
     *
     * @param string $input Input string.
     * @param string|null $with Part to search inside the $string.
     * @return bool Returns true if first input starts with second input, false otherwise.
     */
    public static function startsWith(string $input, ?string $with): bool
    {
        $bytes = static::byteLength($with);
        if ($bytes === 0) {
            return true;
        }

        return strncmp($input, $with, $bytes) === 0;
    }

    /**
     * Check if given string starts with specified substring ignoring case.
     * Binary and multibyte safe.
     *
     * @param string $input Input string.
     * @param string|null $with Part to search inside the $string.
     * @return bool Returns true if first input starts with second input, false otherwise.
     */
    public static function startsWithIgnoringCase(string $input, ?string $with): bool
    {
        $bytes = static::byteLength($with);
        if ($bytes === 0) {
            return true;
        }

        return static::lowercase(static::substring($input, 0, $bytes, '8bit')) === static::lowercase($with);
    }

    /**
     * Check if given string ends with specified substring.
     * Binary and multibyte safe.
     *
     * @param string $input Input string to check.
     * @param string|null $with Part to search inside of the $string.
     * @return bool Returns true if first input ends with second input, false otherwise.
     */
    public static function endsWith(string $input, ?string $with): bool
    {
        $bytes = static::byteLength($with);
        if ($bytes === 0) {
            return true;
        }

        // Warning check, see http://php.net/manual/en/function.substr-compare.php#refsect1-function.substr-compare-returnvalues
        if (static::byteLength($input) < $bytes) {
            return false;
        }

        return substr_compare($input, $with, -$bytes, $bytes) === 0;
    }

    /**
     * Check if given string ends with specified substring.
     * Binary and multibyte safe.
     *
     * @param string $input Input string to check.
     * @param string|null $with Part to search inside of the $string.
     * @return bool Returns true if first input ends with second input, false otherwise.
     */
    public static function endsWithIgnoringCase(string $input, ?string $with): bool
    {
        $bytes = static::byteLength($with);
        if ($bytes === 0) {
            return true;
        }

        return static::lowercase(mb_substr($input, -$bytes, mb_strlen($input, '8bit'), '8bit')) === static::lowercase($with);
    }

    /**
     * Truncates a string from the beginning to the number of characters specified.
     *
     * @param string $input String to process.
     * @param int $length Maximum length of the truncated string including trim marker.
     * @param string $trimMarker String to append to the beginning.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     * @return string
     */
    public static function truncateBegin(string $input, int $length, string $trimMarker = '…', string $encoding = 'UTF-8'): string
    {
        $inputLength = mb_strlen($input, $encoding);

        if ($inputLength <= $length) {
            return $input;
        }

        $trimMarkerLength = mb_strlen($trimMarker, $encoding);
        return self::replaceSubstring($input, $trimMarker, 0, -$length + $trimMarkerLength, $encoding);
    }

    /**
     * Truncates a string in the middle. Keeping start and end.
     * `StringHelper::truncateMiddle('Hello world number 2', 8)` produces "Hell…r 2".
     *
     * @param string $input The string to truncate.
     * @param int $length Maximum length of the truncated string including trim marker.
     * @param string $trimMarker String to append in the middle of truncated string.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     * @return string The truncated string.
     */
    public static function truncateMiddle(string $input, int $length, string $trimMarker = '…', string $encoding = 'UTF-8'): string
    {
        $inputLength = mb_strlen($input, $encoding);

        if ($inputLength <= $length) {
            return $input;
        }

        $trimMarkerLength = mb_strlen($trimMarker, $encoding);
        $start = (int)ceil(($length - $trimMarkerLength) / 2);
        $end = $length - $start - $trimMarkerLength;

        return self::replaceSubstring($input, $trimMarker, $start, -$end, $encoding);
    }

    /**
     * Truncates a string from the end to the number of characters specified.
     *
     * @param string $input The string to truncate.
     * @param int $length Maximum length of the truncated string including trim marker.
     * @param string $trimMarker String to append to the end of truncated string.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     * @return string The truncated string.
     */
    public static function truncateEnd(string $input, int $length, string $trimMarker = '…', string $encoding = 'UTF-8'): string
    {
        $inputLength = mb_strlen($input, $encoding);

        if ($inputLength <= $length) {
            return $input;
        }

        $trimMarkerLength = mb_strlen($trimMarker, $encoding);
        return rtrim(mb_substr($input, 0, $length - $trimMarkerLength, $encoding)) . $trimMarker;
    }

    /**
     * Truncates a string to the number of words specified.
     *
     * @param string $input The string to truncate.
     * @param int $count How many words from original string to include into truncated string.
     * @param string $trimMarker String to append to the end of truncated string.
     * @return string The truncated string.
     */
    public static function truncateWords(string $input, int $count, string $trimMarker = '…'): string
    {
        $words = preg_split('/(\s+)/u', trim($input), -1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($words) / 2 > $count) {
            return implode('', array_slice($words, 0, ($count * 2) - 1)) . $trimMarker;
        }

        return $input;
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
     * Make a string's first character uppercase.
     *
     * @param string $string The string to be processed.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     * @return string
     * @see https://php.net/manual/en/function.ucfirst.php
     */
    public static function uppercaseFirstCharacter(string $string, string $encoding = 'UTF-8'): string
    {
        $firstCharacter = static::substring($string, 0, 1, $encoding);
        $rest = static::substring($string, 1, null, $encoding);

        return static::uppercase($firstCharacter, $encoding) . $rest;
    }

    /**
     * Uppercase the first character of each word in a string.
     *
     * @param string $string The string to be processed.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     * @see https://php.net/manual/en/function.ucwords.php
     * @return string
     */
    public static function uppercaseFirstCharacterInEachWord(string $string, string $encoding = 'UTF-8'): string
    {
        $words = preg_split("/\s/u", $string, -1, PREG_SPLIT_NO_EMPTY);

        $wordsWithUppercaseFirstCharacter = array_map(static function ($word) use ($encoding) {
            return static::uppercaseFirstCharacter($word, $encoding);
        }, $words);

        return implode(' ', $wordsWithUppercaseFirstCharacter);
    }

    /**
     * Get string length.
     *
     * @param string $string String to calculate length for.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     * @see https://php.net/manual/en/function.mb-strlen.php
     * @return int
     */
    public static function length(string $string, string $encoding = 'UTF-8'): int
    {
        return mb_strlen($string, $encoding);
    }

    /**
     * Get part of string.
     *
     * @param string $string To get substring from.
     * @param int $start Character to start at.
     * @param int|null $length Number of characters to get.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     * @see https://php.net/manual/en/function.mb-substr.php
     * @return string
     */
    public static function substring(string $string, int $start, int $length = null, string $encoding = 'UTF-8'): string
    {
        return mb_substr($string, $start, $length, $encoding);
    }

    /**
     * Make a string lowercase.
     *
     * @param string $string String to process.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     * @see https://php.net/manual/en/function.mb-strtolower.php
     * @return string
     */
    public static function lowercase(string $string, string $encoding = 'UTF-8'): string
    {
        return mb_strtolower($string, $encoding);
    }

    /**
     * Make a string uppercase.
     *
     * @param string $string String to process.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     * @see https://php.net/manual/en/function.mb-strtoupper.php
     * @return string
     */
    public static function uppercase(string $string, string $encoding = 'UTF-8'): string
    {
        return mb_strtoupper($string, $encoding);
    }

    /**
     * Replace text within a portion of a string.
     *
     * @param string $string The input string.
     * @param string $replacement The replacement string.
     * @param int $start Position to begin replacing substring at.
     * If start is non-negative, the replacing will begin at the start'th offset into string.
     * If start is negative, the replacing will begin at the start'th character from the end of string.
     * @param int|null $length Length of the substring to be replaced.
     * If given and is positive, it represents the length of the portion of string which is to be replaced.
     * If it is negative, it represents the number of characters from the end of string at which to stop replacing.
     * If it is not given, then it will default to the length of the string; i.e. end the replacing at the end of string.
     * If length is zero then this function will have the effect of inserting replacement into string at the given start offset.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     * @return string
     */
    public static function replaceSubstring(string $string, string $replacement, int $start, ?int $length = null, string $encoding = 'UTF-8'): string
    {
        $stringLength = mb_strlen($string, $encoding);

        if ($start < 0) {
            $start = \max(0, $stringLength + $start);
        } elseif ($start > $stringLength) {
            $start = $stringLength;
        }

        if ($length !== null && $length < 0) {
            $length = \max(0, $stringLength - $start + $length);
        } elseif ($length === null || $length > $stringLength) {
            $length = $stringLength;
        }

        if (($start + $length) > $stringLength) {
            $length = $stringLength - $start;
        }

        return mb_substr($string, 0, $start, $encoding) . $replacement . mb_substr($string, $start + $length, $stringLength - $start - $length, $encoding);
    }

    /**
     * Converts a list of words into a sentence.
     *
     * Special treatment is done for the last few words. For example,
     *
     * ```php
     * $words = ['Spain', 'France'];
     * echo Inflector::sentence($words);
     * // output: Spain and France
     *
     * $words = ['Spain', 'France', 'Italy'];
     * echo Inflector::sentence($words);
     * // output: Spain, France and Italy
     *
     * $words = ['Spain', 'France', 'Italy'];
     * echo Inflector::sentence($words, ' & ');
     * // output: Spain, France & Italy
     * ```
     *
     * @param array $words The words to be converted into an string.
     * @param string $twoWordsConnector The string connecting words when there are only two. Default to " and ".
     * @param string|null $lastWordConnector The string connecting the last two words. If this is null, it will
     * take the value of `$twoWordsConnector`.
     * @param string $connector The string connecting words other than those connected by
     * $lastWordConnector and $twoWordsConnector.
     * @return string The generated sentence.
     */
    public static function sentence(array $words, string $twoWordsConnector = ' and ', ?string $lastWordConnector = null, string $connector = ', '): ?string
    {
        if ($lastWordConnector === null) {
            $lastWordConnector = $twoWordsConnector;
        }
        switch (count($words)) {
            case 0:
                return '';
            case 1:
                return reset($words);
            case 2:
                return implode($twoWordsConnector, $words);
            default:
                return implode($connector, \array_slice($words, 0, -1)) . $lastWordConnector . end($words);
        }
    }
}
