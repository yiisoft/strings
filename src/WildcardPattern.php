<?php

declare(strict_types=1);

namespace Yiisoft\Strings;

/**
 * A wildcard pattern to match strings against.
 *
 * - `\` escapes other special characters if usage of escape character is not turned off.
 * - `*` matches any string including the empty string except it has a delimiter (`/` and `\` by default).
  * - `**` matches any string including the empty string and delimiters.
 * - `?` matches any single character.
 * - `[seq]` matches any character in seq.
 * - `[a-z]` matches any character from a to z.
 * - `[!seq]` matches any character not in seq.
 * - `[[:alnum:]]` matches POSIX style character classes,
 *   see {@see https://www.php.net/manual/en/regexp.reference.character-classes.php}.
 */
final class WildcardPattern
{
    private bool $ignoreCase = false;
    private string $pattern;

    /**
     * @var string[]
     */
    private array $delimiters;

    /**
     * @param string $pattern The shell wildcard pattern to match against.
     * @param string[] $delimiters Delimiters to consider for "*" (`/` and `\` by default).
     */
    public function __construct(string $pattern, array $delimiters = ['\\\\', '/'])
    {
        $this->pattern = $pattern;
        $this->delimiters = $delimiters;
    }

    /**
     * Checks if the passed string would match the given shell wildcard pattern.
     *
     * @param string $string The tested string.
     *
     * @return bool Whether the string matches pattern or not.
     */
    public function match(string $string): bool
    {
        if ($this->pattern === '**') {
            return true;
        }

        $pattern = $this->pattern;

        $replacements = [
            '\*\*' => '.*',
            '\\\\\\\\' => '\\\\',
            '\\\\\\*' => '[*]',
            '\\\\\\?' => '[?]',
            '\\\\\\[' => '[\[]',
            '\\\\\\]' => '[\]]',
        ];

        if ($this->delimiters === []) {
            $replacements += [
                '\*' => '.*',
                '\?' => '?',
            ];
        } else {
            $notDelimiters = '[^' . preg_quote(implode('', $this->delimiters), '#') . ']';
            $replacements += [
                '\*' => "$notDelimiters*",
                '\?' => $notDelimiters,
            ];
        }

        $replacements += [
            '\[\!' => '[^',
            '\[' => '[',
            '\]' => ']',
            '\-' => '-',
        ];

        $pattern = strtr(preg_quote($pattern, '#'), $replacements);
        $pattern = '#^' . $pattern . '$#us';

        if ($this->ignoreCase) {
            $pattern .= 'i';
        }

        return preg_match($pattern, $string) === 1;
    }

    /**
     * Make pattern case insensitive.
     *
     * @param bool $flag
     *
     * @return self
     */
    public function ignoreCase(bool $flag = true): self
    {
        $new = clone $this;
        $new->ignoreCase = $flag;
        return $new;
    }

    /**
     * Returns whether the pattern contains a dynamic part i.e.
     * has unescaped "*",  "{", "?", or "[" character.
     *
     * @param string $pattern The pattern to check.
     *
     * @return bool Whether the pattern contains a dynamic part.
     */
    public static function isDynamic(string $pattern): bool
    {
        $pattern = preg_replace('/\\\\./', '', $pattern);
        return (bool)preg_match('/[*{?\[]/', $pattern);
    }

    /**
     * Escapes pattern characters in a string.
     *
     * @param string $string Source string.
     *
     * @return string String with pattern characters escaped.
     */
    public static function quote(string $string): string
    {
        return preg_replace('#([\\\\?*\\[\\]])#', '\\\\$1', $string);
    }
}
