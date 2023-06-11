<?php

declare(strict_types=1);

namespace Yiisoft\Strings;

final class CombinedRegexp
{
    private string $freeQuoter;
    private string $quoteReplacer;
    private string $compiledPattern;

    public function __construct(
        private array $patterns,
        private string $regexpQuoter = '/'
    ) {
        $this->freeQuoter = $this->regexpQuoter === '/' ? '~' : '/';
        $this->quoteReplacer = preg_quote($this->regexpQuoter, $this->regexpQuoter);
        $this->compiledPattern = $this->compilePatterns($this->patterns);
    }

    public function getCompiledPattern(): string
    {
        return $this->compiledPattern;
    }

    public function matchAny(string $string, string $flags = 'i'): bool
    {
        $pattern = $this->compiledPattern . $flags;

        return preg_match($pattern, $string) === 1;
    }

    public function matchPattern(string $string)
    {
        return $this->patterns[$this->matchPatternPosition($string)];
    }

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

    private function compilePatterns(array $patterns): string
    {
        $quotedPatterns = [];
        for ($i = 0; $i < count($patterns); $i++) {
            $quotedPatterns[] = preg_replace(
                $this->freeQuoter . $this->regexpQuoter . $this->freeQuoter,
                $this->quoteReplacer,
                $patterns[$i]
            ) . str_repeat('()', $i);
        }
        $combinedRegexps = '(?|' . implode('|', $quotedPatterns) . ')';

        return $this->freeQuoter . $combinedRegexps . $this->freeQuoter;
    }
}
