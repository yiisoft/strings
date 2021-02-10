<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Strings\WildcardPattern;

final class WildcardPatternTest extends TestCase
{
    /**
     * Data provider for {@see testMatchWildcard()}.
     *
     * @return array test data.
     */
    public function dataProviderMatchWildcard(): array
    {
        return [
            // *
            ['*', 'any', true],
            ['*', 'any/path', false],
            ['*', '.dotenv', true],
            ['*', '', true],
            ['begin*end', 'begin-middle-end', true],
            ['begin*end', 'beginend', true],
            ['begin*end', 'begin-d', false],
            ['*end', 'beginend', true],
            ['*end', 'begin', false],
            ['begin*', 'begin-end', true],
            ['begin*', 'end', false],
            ['begin*', 'before-begin', false],

            // * with slashes
            ['begin/*/end', 'begin/middle/end', true],
            ['begin/*/end', 'begin/two/steps/end', false],
            ['begin/*/end', 'begin/end', false],
            ['begin\\\\*\\\\end', 'begin\middle\end', true],
            ['begin\\\\*\\\\end', 'begin\two\steps\end', false],
            ['begin\\\\*\\\\end', 'begin\end', false],

            // **
            ['begin/**/end', 'begin/middle/end', true],
            ['begin/**/end', 'begin/two/steps/end', true],
            ['begin/**/end', 'begin/end', false],
            ['begin\\\\**\\\\end', 'begin\middle\end', true],
            ['begin\\\\**\\\\end', 'begin\two\steps\end', true],
            ['begin\\\\**\\\\end', 'begin\end', false],

            // ?
            ['begin?end', 'begin1end', true],
            ['begin?end', 'beginend', false],
            ['begin??end', 'begin12end', true],
            ['begin??end', 'begin1end', false],

            // []
            ['gr[ae]y', 'gray', true],
            ['gr[ae]y', 'grey', true],
            ['gr[ae]y', 'groy', false],
            ['a[2-8]', 'a1', false],
            ['a[2-8]', 'a3', true],
            ['[][!]', ']', true],
            ['[-1]', '-', true],
            ['[.-0]', 'any/path', false],

            // [!]
            ['gr[!ae]y', 'gray', false],
            ['gr[!ae]y', 'grey', false],
            ['gr[!ae]y', 'groy', true],
            ['a[!2-8]', 'a1', true],
            ['a[!2-8]', 'a3', false],

            // -
            ['a-z', 'a-z', true],
            ['a-z', 'a-c', false],

            // Dots
            ['begin.*.end', 'begin.middle.end', true],
            ['begin.*.end', 'begin.two.steps.end', true],
            ['begin.*.end', 'begin.end', false],

            // Case insensitive matching
            ['begin*end', 'BEGIN-middle-END', false],
            ['begin*end', 'BEGIN-middle-END', true, ['caseSensitive' => false]],

            // Do not use \ as escaping character
            ['\*\?', '*?', true],
            ['\*\?', 'zz', false],
            ['begin\*\end', 'begin\middle\end', true, ['escape' => false]],
            ['begin\*\end', 'begin\two\steps\end', false, ['escape' => false]],
            ['begin\*\end', 'begin\end', false, ['escape' => false]],
            ['begin\*\end', 'begin\middle\end', true, ['filePath' => true, 'escape' => false]],
            ['begin\*\end', 'begin\two\steps\end', false, ['filePath' => true, 'escape' => false]],
        ];
    }

    /**
     * @dataProvider dataProviderMatchWildcard
     *
     * @param string $pattern
     * @param string $string
     * @param bool $expectedResult
     * @param array $options
     */
    public function testMatchWildcard(string $pattern, string $string, bool $expectedResult, array $options = []): void
    {
        $wildcardPattern = $this->getWildcardPattern($pattern, $options);
        $this->assertSame($expectedResult, $wildcardPattern->match($string));
    }

    private function getWildcardPattern(string $pattern, array $options): WildcardPattern
    {
        $wildcardPattern = new WildcardPattern($pattern);
        if (isset($options['caseSensitive']) && $options['caseSensitive'] === false) {
            $wildcardPattern = $wildcardPattern->ignoreCase();
        }
        if (isset($options['escape']) && $options['escape'] === false) {
            $wildcardPattern = $wildcardPattern->withoutEscape();
        }

        return $wildcardPattern;
    }

    public function testDisableOptions(): void
    {
        $wildcardPattern = (new WildcardPattern('\*42'))
            ->withoutEscape()
            ->withoutEscape(false);
        $this->assertTrue($wildcardPattern->match('*42'));

        $wildcardPattern = (new WildcardPattern('abc42'))
            ->ignoreCase()
            ->ignoreCase(false);
        $this->assertFalse($wildcardPattern->match('ABC42'));
    }

    public function testImmutability(): void
    {
        $original = new WildcardPattern('*');
        $this->assertNotSame($original, $original->ignoreCase());
        $this->assertNotSame($original, $original->withoutEscape());
    }

    /**
     * @dataProvider isDynamicDataProvider
     */
    public function testIsDynamic(string $pattern, bool $expected): void
    {
        $this->assertSame($expected, WildcardPattern::isDynamic($pattern));
    }

    public function isDynamicDataProvider(): array
    {
        return [
            'not-dynamic' => ['just-some-string', false],
            'char' => ['just-some-string?', true],
            'escaped-1' => ['just-some-string?', true],
            'escaped-2' => ['just-some-string\\?', false],
            'escaped-3' => ['just-some-string\\\\?', true],
        ];
    }

    public function customDelimitersProvider(): array
    {
        return [
            'empty' => ['begin*end', 'begin/end', [], true],
            'dot' => ['begin*end', 'begin.end', ['.'], false],
            'multiple' => ['begin*end', 'begin$end', ['.', '$'], false],
        ];
    }

    /**
     * @dataProvider customDelimitersProvider
     */
    public function testCustomDelimiters(string $pattern, string $string, array $delimiters, bool $expected): void
    {
        $wildcardPattern = $wildcardPattern = new WildcardPattern($pattern, $delimiters);
        $this->assertSame($expected, $wildcardPattern->match($string));
    }
}
