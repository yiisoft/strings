<?php

declare(strict_types=1);

namespace Yiisoft\Strings;

/**
 * A wildcard pattern to match strings against.
 *
 * - `\` escapes other special characters if usage of escape character is not turned off.
 * - `*` matches any string including the empty string. Slashes do not match.
  * - `**` matches any string including the empty string and slashes.
 * - `?` matches any single character.
 * - `[seq]` matches any character in seq.
 * - `[a-z]` matches any character from a to z.
 * - `[!seq]` matches any character not in seq.
 * - `[[:alnum:]]` matches POSIX style character classes,
 *   see {@see https://www.php.net/manual/en/regexp.reference.character-classes.php}.
 */
final class WildcardPattern
{
    private bool $withoutEscape = false;
    private bool $matchLeadingPeriodExactly = false;
    private bool $ignoreCase = false;
    private bool $matchEnding = false;
    private string $pattern;

    /**
     * @param string $pattern The shell wildcard pattern to match against.
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
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
        if ($this->pattern === '**' && !$this->matchLeadingPeriodExactly) {
            return true;
        }

        $pattern = $this->pattern;

        if ($this->matchLeadingPeriodExactly) {
            $pattern = preg_replace('/^[*?]/', '[!.]', $pattern);
        }

        $replacements = [
            '\*\*' => '.*',
            '\\\\\\\\' => '\\\\',
            '\\\\\\*' => '[*]',
            '\\\\\\?' => '[?]',
            '\*' => '[^/\\\\]*',
            '\?' => '[^/\\\\]',
            '\[\!' => '[^',
            '\[' => '[',
            '\]' => ']',
            '\-' => '-',
        ];

        if ($this->withoutEscape) {
            unset($replacements['\\\\\\\\'], $replacements['\\\\\\*'], $replacements['\\\\\\?']);
        }

        $pattern = strtr(preg_quote($pattern, '#'), $replacements);
        $pattern = '#' . ($this->matchEnding ? '' : '^') . $pattern . '$#us';

        if ($this->ignoreCase) {
            $pattern .= 'i';
        }

        return preg_match($pattern, $string) === 1;
    }

    /**
     * Disables using `\` to escape following special character. `\` becomes regular character.
     *
     * @param bool $flag
     *
     * @return self
     */
    public function withoutEscape(bool $flag = true): self
    {
        $new = clone $this;
        $new->withoutEscape = $flag;
        return $new;
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
     * Do not match `.` character at the beginning of string with wildcards.
     * Useful for matching file paths.
     *
     * @param bool $flag
     *
     * @return self
     */
    public function exactLeadingPeriod(bool $flag = true): self
    {
        $new = clone $this;
        $new->matchLeadingPeriodExactly = $flag;
        return $new;
    }

    /**
     * Match ending only.
     * By default wildcard pattern matches string exactly. By using this mode, beginning of the string could be anything.
     *
     * @param bool $flag
     *
     * @return self
     */
    public function matchEnding(bool $flag = true): self
    {
        $new = clone $this;
        $new->matchEnding = $flag;
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
        $position = strpos($pattern, '*');
        if ($position !== false && ($position === 0 || $pattern[$position - 1] !== '\\')) {
            return true;
        }

        $position = strpos($pattern, '{');
        if ($position !== false && ($position === 0 || $pattern[$position - 1] !== '\\')) {
            return true;
        }

        $position = strpos($pattern, '?');
        if ($position !== false && ($position === 0 || $pattern[$position - 1] !== '\\')) {
            return true;
        }

        $position = strpos($pattern, '[');
        return $position !== false && ($position === 0 || $pattern[$position - 1] !== '\\');
    }
}
