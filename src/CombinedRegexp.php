<?php

declare(strict_types=1);

namespace Yiisoft\Strings;

/**
 * `CombinedRegexp` optimizes matching of multiple regular expressions.
 * Read more about the concept in
 * {@see https://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html}.
 */
final class CombinedRegexp
{
    private const REGEXP_DELIMITER = '/';
    private const QUOTE_REPLACER = '\\/';

    /**
     * @var string[]
     */
    private array $patterns;
    private string $compiledPattern;

    /**
     * @param string[] $patterns Regular expressions to combine.
     * @param string $flags Flags to apply to all regular expressions.
     */
    public function __construct(
        array $patterns,
        string $flags = ''
    ) {
        if (count($patterns) === 0) {
            throw new \InvalidArgumentException('At least one pattern should be specified.');
        }
        $this->patterns = $patterns;
        $this->compiledPattern = $this->compilePatterns($patterns) . $flags;
    }

    /**
     * @return string The compiled pattern.
     */
    public function getCompiledPattern(): string
    {
        return $this->compiledPattern;
    }

    /**
     * Returns `true` whether the given string matches any of the patterns, `false` - otherwise.
     */
    public function matches(string $string): bool
    {
        return preg_match($this->compiledPattern, $string) === 1;
    }

    /**
     * Returns pattern that matches the given string.
     * @throws \Exception if the string does not match any of the patterns.
     */
    public function getMatchingPattern(string $string): string
    {
        return $this->patterns[$this->getMatchingPatternPosition($string)];
    }

    /**
     * Returns position of the pattern that matches the given string.
     * @throws \Exception if the string does not match any of the patterns.
     */
    public function getMatchingPatternPosition(string $string): int
    {
        $match = preg_match($this->compiledPattern, $string, $matches);
        if ($match !== 1) {
            throw new \Exception(
                sprintf(
                    'Failed to match pattern "%s" with string "%s".',
                    $this->getCompiledPattern(),
                    $string,
                )
            );
        }

        return count($matches) - 1;
    }

    /**
     * @param string[] $patterns
     */
    private function compilePatterns(array $patterns): string
    {
        $quotedPatterns = [];

        /**
         * Possible mutant escaping, but it's ok for our case.
         * It doesn't matter where to place `()` in the pattern:
         * https://regex101.com/r/lE1Q1S/1, https://regex101.com/r/rWg7Fj/1
         */
        for ($i = 0; $i < count($patterns); $i++) {
            $quotedPatterns[] = $patterns[$i] . str_repeat('()', $i);
        }
        $combinedRegexps = '(?|' . strtr(
            implode('|', $quotedPatterns),
            [self::REGEXP_DELIMITER => self::QUOTE_REPLACER]
        ) . ')';

        return self::REGEXP_DELIMITER . $combinedRegexps . self::REGEXP_DELIMITER;
    }
}
