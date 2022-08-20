<?php

declare(strict_types=1);

namespace Yiisoft\Strings;

use InvalidArgumentException;

use function array_slice;
use function count;
use function function_exists;
use function max;
use function mb_strlen;
use function mb_strtolower;
use function mb_strtoupper;
use function mb_substr;
use function str_ends_with;
use function str_starts_with;

/**
 * Provides static methods to work with strings.
 */
final class StringHelper
{
    /**
     * Returns the number of bytes in the given string.
     * This method ensures the string is treated as a byte array even if `mbstring.func_overload` is turned on
     * by using {@see mb_strlen()}.
     *
     * @param string|null $input The string being measured for length.
     *
     * @return int The number of bytes in the given string.
     */
    public static function byteLength(?string $input): int
    {
        return mb_strlen((string)$input, '8bit');
    }

    /**
     * Returns the portion of string specified by the start and length parameters.
     * This method ensures the string is treated as a byte array by using `mb_substr()`.
     *
     * @param string $input The input string. Must be one character or longer.
     * @param int $start The starting position.
     * @param int|null $length The desired portion length. If not specified or `null`, there will be
     * no limit on length i.e. the output will be until the end of the string.
     *
     * @return string The extracted part of string, or FALSE on failure or an empty string.
     *
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
     *
     * @return string The trailing name component of the given path.
     *
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
     *
     * @return string The parent directory's path.
     *
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
     * Get part of string.
     *
     * @param string $string To get substring from.
     * @param int $start Character to start at.
     * @param int|null $length Number of characters to get.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     *
     * @see https://php.net/manual/en/function.mb-substr.php
     *
     * @return string
     */
    public static function substring(string $string, int $start, int $length = null, string $encoding = 'UTF-8'): string
    {
        return mb_substr($string, $start, $length, $encoding);
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
     *
     * @return string
     */
    public static function replaceSubstring(string $string, string $replacement, int $start, ?int $length = null, string $encoding = 'UTF-8'): string
    {
        $stringLength = mb_strlen($string, $encoding);

        if ($start < 0) {
            $start = max(0, $stringLength + $start);
        } elseif ($start > $stringLength) {
            $start = $stringLength;
        }

        if ($length !== null && $length < 0) {
            $length = max(0, $stringLength - $start + $length);
        } elseif ($length === null || $length > $stringLength) {
            $length = $stringLength;
        }

        if (($start + $length) > $stringLength) {
            $length = $stringLength - $start;
        }

        return mb_substr($string, 0, $start, $encoding) . $replacement . mb_substr($string, $start + $length, $stringLength - $start - $length, $encoding);
    }

    /**
     * Check if given string starts with specified substring.
     * Binary and multibyte safe.
     *
     * @param string $input Input string.
     * @param string|null $with Part to search inside the $string.
     *
     * @return bool Returns true if first input starts with second input, false otherwise.
     */
    public static function startsWith(string $input, ?string $with): bool
    {
        if ($with === null) {
            return true;
        }

        if (function_exists('\str_starts_with')) {
            return str_starts_with($input, $with);
        }

        $bytes = self::byteLength($with);
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
     *
     * @return bool Returns true if first input starts with second input, false otherwise.
     */
    public static function startsWithIgnoringCase(string $input, ?string $with): bool
    {
        $bytes = self::byteLength($with);
        if ($bytes === 0) {
            return true;
        }

        /**
         * @psalm-suppress PossiblyNullArgument
         */
        return self::lowercase(self::substring($input, 0, $bytes, '8bit')) === self::lowercase($with);
    }

    /**
     * Check if given string ends with specified substring.
     * Binary and multibyte safe.
     *
     * @param string $input Input string to check.
     * @param string|null $with Part to search inside of the $string.
     *
     * @return bool Returns true if first input ends with second input, false otherwise.
     */
    public static function endsWith(string $input, ?string $with): bool
    {
        if ($with === null) {
            return true;
        }

        if (function_exists('\str_ends_with')) {
            return str_ends_with($input, $with);
        }

        $bytes = self::byteLength($with);
        if ($bytes === 0) {
            return true;
        }

        // Warning check, see http://php.net/manual/en/function.substr-compare.php#refsect1-function.substr-compare-returnvalues
        if (self::byteLength($input) < $bytes) {
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
     *
     * @return bool Returns true if first input ends with second input, false otherwise.
     */
    public static function endsWithIgnoringCase(string $input, ?string $with): bool
    {
        $bytes = self::byteLength($with);
        if ($bytes === 0) {
            return true;
        }

        /**
         * @psalm-suppress PossiblyNullArgument
         */
        return self::lowercase(mb_substr($input, -$bytes, mb_strlen($input, '8bit'), '8bit')) === self::lowercase($with);
    }

    /**
     * Truncates a string from the beginning to the number of characters specified.
     *
     * @param string $input String to process.
     * @param int $length Maximum length of the truncated string including trim marker.
     * @param string $trimMarker String to append to the beginning.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     *
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
     *
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
     *
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
     *
     * @return string The truncated string.
     */
    public static function truncateWords(string $input, int $count, string $trimMarker = '…'): string
    {
        $words = preg_split('/(\s+)/u', trim($input), -1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($words) / 2 > $count) {
            /** @var string[] $words */
            $words = array_slice($words, 0, ($count * 2) - 1);
            return implode('', $words) . $trimMarker;
        }

        return $input;
    }

    /**
     * Get string length.
     *
     * @param string $string String to calculate length for.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     *
     * @see https://php.net/manual/en/function.mb-strlen.php
     *
     * @return int
     */
    public static function length(string $string, string $encoding = 'UTF-8'): int
    {
        return mb_strlen($string, $encoding);
    }

    /**
     * Counts words in a string.
     *
     * @param string $input
     *
     * @return int
     */
    public static function countWords(string $input): int
    {
        return count(preg_split('/\s+/u', $input, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * Make a string lowercase.
     *
     * @param string $string String to process.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     *
     * @see https://php.net/manual/en/function.mb-strtolower.php
     *
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
     *
     * @see https://php.net/manual/en/function.mb-strtoupper.php
     *
     * @return string
     */
    public static function uppercase(string $string, string $encoding = 'UTF-8'): string
    {
        return mb_strtoupper($string, $encoding);
    }

    /**
     * Make a string's first character uppercase.
     *
     * @param string $string The string to be processed.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     *
     * @return string
     *
     * @see https://php.net/manual/en/function.ucfirst.php
     */
    public static function uppercaseFirstCharacter(string $string, string $encoding = 'UTF-8'): string
    {
        $firstCharacter = self::substring($string, 0, 1, $encoding);
        $rest = self::substring($string, 1, null, $encoding);

        return self::uppercase($firstCharacter, $encoding) . $rest;
    }

    /**
     * Uppercase the first character of each word in a string.
     *
     * @param string $string The string to be processed.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     *
     * @see https://php.net/manual/en/function.ucwords.php
     *
     * @return string
     */
    public static function uppercaseFirstCharacterInEachWord(string $string, string $encoding = 'UTF-8'): string
    {
        $words = preg_split('/\s/u', $string, -1, PREG_SPLIT_NO_EMPTY);

        $wordsWithUppercaseFirstCharacter = array_map(static function (string $word) use ($encoding) {
            return self::uppercaseFirstCharacter($word, $encoding);
        }, $words);

        return implode(' ', $wordsWithUppercaseFirstCharacter);
    }

    /**
     * Encodes string into "Base 64 Encoding with URL and Filename Safe Alphabet" (RFC 4648).
     *
     * > Note: Base 64 padding `=` may be at the end of the returned string.
     * > `=` is not transparent to URL encoding.
     *
     * @see https://tools.ietf.org/html/rfc4648#page-7
     *
     * @param string $input The string to encode.
     *
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
     *
     * @param string $input Encoded string.
     *
     * @return string Decoded string.
     */
    public static function base64UrlDecode(string $input): string
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * Split a string to array with non-empty lines.
     * Whitespace from the beginning and end of a each line will be stripped.
     *
     * @param string $string The input string.
     * @param string $separator The boundary string. It is a part of regular expression
     * so should be taken into account or properly escaped with {@see preg_quote()}.
     *
     * @return array
     */
    public static function split(string $string, string $separator = '\R'): array
    {
        $string = preg_replace('(^\s*|\s*$)', '', $string);
        return preg_split('~\s*' . $separator . '\s*~', $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @param string $path The path of where do you want to write a value to `$array`. The path can be described by
     * a string when each key should be separated by delimiter. If a path item contains delimiter, it can be escaped
     * with "\" (backslash) or a custom delimiter can be used.
     * @param string $delimiter A separator, used to parse string key for embedded object property retrieving. Defaults
     * to "." (dot).
     * @param string $escapeCharacter An escape character, used to escape delimiter. Defaults to "\" (backslash).
     * @param bool $preserveDelimiterEscaping Whether to preserve delimiter escaping in the items of final array (in
     * case of using string as an input). When `false`, "\" (backslashes) are removed. For a "." as delimiter, "."
     * becomes "\.". Defaults to `false`.
     *
     * @return string[]
     */
    public static function parsePath(
        string $path,
        string $delimiter = '.',
        string $escapeCharacter = '\\',
        bool $preserveDelimiterEscaping = false
    ): array {
        if (strlen($delimiter) !== 1) {
            throw new InvalidArgumentException('Only 1 character is allowed for delimiter.');
        }

        if (strlen($escapeCharacter) !== 1) {
            throw new InvalidArgumentException('Only 1 escape character is allowed.');
        }

        if ($delimiter === $escapeCharacter) {
            throw new InvalidArgumentException('Delimiter and escape character must be different.');
        }

        if ($path === '') {
            return [];
        }

        $matches = preg_split(
            sprintf(
                '/(?<!%1$s)((?>%1$s%1$s)*)%2$s/',
                preg_quote($escapeCharacter, '/'),
                preg_quote($delimiter, '/')
            ),
            $path,
            -1,
            PREG_SPLIT_OFFSET_CAPTURE
        );
        $result = [];
        $countResults = count($matches);
        for ($i = 1; $i < $countResults; $i++) {
            $l = $matches[$i][1] - $matches[$i - 1][1] - strlen($matches[$i - 1][0]) - 1;
            $result[] = $matches[$i - 1][0] . ($l > 0 ? str_repeat($escapeCharacter, $l) : '');
        }
        $result[] = $matches[$countResults - 1][0];

        if ($preserveDelimiterEscaping === true) {
            return $result;
        }

        return array_map(
            static function (string $key) use ($delimiter, $escapeCharacter): string {
                return str_replace(
                    [
                        $escapeCharacter . $escapeCharacter,
                        $escapeCharacter . $delimiter,
                    ],
                    [
                        $escapeCharacter,
                        $delimiter,
                    ],
                    $key
                );
            },
            $result
        );
    }
}
