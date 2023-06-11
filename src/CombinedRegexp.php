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
    private string $freeQuoter;
    private string $quoteReplacer;
    private string $compiledPattern;

    public function __construct(
        /**
         * @var string[]
         */
        private array $patterns,
        private string $regexpQuoter = '/'
    ) {
        $this->freeQuoter = $this->regexpQuoter === '/' ? '~' : '/';
        $this->quoteReplacer = preg_quote($this->regexpQuoter, $this->regexpQuoter);
        $this->compiledPattern = $this->compilePatterns($this->patterns);
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
    public function matchAny(string $string, string $flags = 'i'): bool
    {
        $pattern = $this->compiledPattern . $flags;

        return preg_match($pattern, $string) === 1;
    }

    /**
     * Returns pattern that matches the given string.
     * @throws \Exception if the string does not match any of the patterns.
     */
    public function matchPattern(string $string): string
    {
        return $this->patterns[$this->matchPatternPosition($string)];
    }

    /**
     * Returns position of the pattern that matches the given string.
     * @throws \Exception if the string does not match any of the patterns.
     */
    public function matchPatternPosition(string $string): int
    {
        $match = preg_match($this->compiledPattern, $string, $matches);
        if ($match !== 1) {
            throw new \Exception(
                sprintf(
                    'Failed to match pattern "%s" with string "%s".',
                    $this->compiledPattern,
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
        for ($i = 0; $i < count($patterns); $i++) {
            $quotedPatterns[] = preg_replace(
                    $this->freeQuoter . $this->regexpQuoter . $this->freeQuoter,
                    $this->quoteReplacer,
                    $patterns[$i],
                ) . str_repeat('()', $i);
        }
        $combinedRegexps = '(?|' . implode('|', $quotedPatterns) . ')';

        return $this->freeQuoter . $combinedRegexps . $this->freeQuoter;
    }
}
