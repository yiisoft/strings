<?php

declare(strict_types=1);

namespace Yiisoft\Strings;

/**
 * `CombinedRegexp` optimizes matching of multiple regular expressions.
 * Read more about the concept in
 * {@see https://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html}.
 */
final class MemoizedCombinedRegexp
{
    private CombinedRegexp $combinedRegexp;

    private array $results = [];

    /**
     * @param string[] $patterns Regular expressions to combine.
     * @param string $flags Flags to apply to all regular expressions.
     */
    public function __construct(
        private array $patterns,
        string $flags = ''
    ) {
        $this->combinedRegexp = new CombinedRegexp($patterns, $flags);
    }

    /**
     * @return string The compiled pattern.
     */
    public function getCompiledPattern(): string
    {
        return $this->combinedRegexp->getCompiledPattern();
    }

    /**
     * Returns `true` whether the given string matches any of the patterns, `false` - otherwise.
     */
    public function matches(string $string): bool
    {
        $this->evaluate($string);

        return $this->results[$string]['matches'];
    }

    /**
     * Returns pattern that matches the given string.
     * @throws \Exception if the string does not match any of the patterns.
     */
    public function getMatchingPattern(string $string): string
    {
        $this->evaluate($string);

        return $this->patterns[$this->getMatchingPatternPosition($string)];
    }

    /**
     * Returns position of the pattern that matches the given string.
     * @throws \Exception if the string does not match any of the patterns.
     */
    public function getMatchingPatternPosition(string $string): int
    {
        $this->evaluate($string);

        return $this->results[$string]['position'] ?? throw new \Exception(
            sprintf(
                'Failed to match pattern "%s" with string "%s".',
                $this->getCompiledPattern(),
                $string,
            )
        );
    }

    protected function evaluate(string $string): void
    {
        if (isset($this->results[$string])) {
            return;
        }
        try {
            $position = $this->combinedRegexp->getMatchingPatternPosition($string);

            $this->results[$string]['matches'] = true;
            $this->results[$string]['position'] = $position;
        } catch (\Exception) {
            $this->results[$string]['matches'] = false;
        }
    }
}
