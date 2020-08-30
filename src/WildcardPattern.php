<?php

declare(strict_types=1);

namespace Yiisoft\Strings;

/**
 * A shell wildcard pattern to match strings against.
 *
 * - `\` escapes other special characters if usage of escape character is not turned off.
 * - `*` matches any string, including the empty string.
 * - `?` matches any single character.
 * - `[seq]` matches any character in seq.
 * - `[a-z]` matches any character from a to z.
 * - `[!seq]` matches any character not in seq.
 * - `[[:alnum:]]` matches POSIX style character classes,
 *   see {@see https://www.php.net/manual/en/regexp.reference.character-classes.php}.
 *
 * @see https://www.man7.org/linux/man-pages/man7/glob.7.html
 *
 * The class emulates {@see fnmatch()} using PCRE since it is not uniform across operating systems
 * and may not be available.
 */
final class WildcardPattern
{
    private bool $withoutEscape = false;
    private bool $matchSlashesExactly = false;
    private bool $matchLeadingPeriodExactly = false;
    private bool $ignoreCase = false;
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
     * @return bool Whether the string matches pattern or not.
     */
    public function match(string $string): bool
    {
        if ($this->pattern === '*' && !$this->matchSlashesExactly) {
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

        if ($this->withoutEscape) {
            unset($replacements['\\\\\\\\'], $replacements['\\\\\\*'], $replacements['\\\\\\?']);
        }

        if ($this->matchSlashesExactly) {
            $replacements['\*'] = '[^/\\\\]*';
            $replacements['\?'] = '[^/\\\\]';
        }

        $pattern = strtr(preg_quote($this->pattern, '#'), $replacements);
        $pattern = '#^' . $pattern . '$#us';

        if ($this->ignoreCase) {
            $pattern .= 'i';
        }

        return preg_match($pattern, $string) === 1;
    }

    /**
     * Disables using `\` to escape following special character. `\` becomes regular character.
     * @return self
     */
    public function withoutEscape(): self
    {
        $new = clone $this;
        $new->withoutEscape = true;
        return $new;
    }

    /**
     * Do not match `/` character with wildcards. The only way to match `/` is with an explicit `/` in pattern.
     * Useful for matching file paths. Use with {@see withExactLeadingPeriod()}.
     * @return self
     */
    public function withExactSlashes(): self
    {
        $new = clone $this;
        $new->matchSlashesExactly = true;
        return $new;
    }

    /**
     * Make pattern case insensitive.
     * @return self
     */
    public function ignoreCase(): self
    {
        $new = clone $this;
        $new->ignoreCase = true;
        return $new;
    }

    /**
     * Do not match `.` character at the beginning of string with wildcards.
     * Useful for matching file paths. Use with {@see withExactSlashes()}.
     * @return self
     */
    public function withExactLeadingPeriod(): self
    {
        $new = clone $this;
        $new->matchLeadingPeriodExactly = true;
        return $new;
    }
}
