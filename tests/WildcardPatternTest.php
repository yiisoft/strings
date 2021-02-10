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
            ['from/**/b', 'from/a/to/b', true],
            ['from\\\\**\\\\b', 'from\a\to\b', true],
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
            // [!]
            ['gr[!ae]y', 'gray', false],
            ['gr[!ae]y', 'grey', false],
            ['gr[!ae]y', 'groy', true],
            ['a[!2-8]', 'a1', true],
            ['a[!2-8]', 'a3', false],
            // -
            ['a-z', 'a-z', true],
            ['a-z', 'a-c', false],
            // dots
            ['begin.*.end', 'begin.middle.end', true],
            ['begin.*.end', 'begin.two.steps.end', true],
            ['begin.*.end', 'begin.end', false],
            // leading period
            ['.test', '.test', true],
            ['*test', '.test', true],
            ['.test', '.test', true, ['leadingPeriod' => true]],
            ['*test', '.test', false, ['leadingPeriod' => true]],
            ['*', '.test', false, ['leadingPeriod' => true]],
            // case
            ['begin*end', 'BEGIN-middle-END', false],
            ['begin*end', 'BEGIN-middle-END', true, ['caseSensitive' => false]],
            // file path
            ['begin/*/end', 'begin/middle/end', true],
            ['begin/*/end', 'begin/two/steps/end', false],
            ['begin\\\\*\\\\end', 'begin\middle\end', true],
            ['begin\\\\*\\\\end', 'begin\two\steps\end', false],
            ['*', 'any', true],
            ['*', 'any/path', false],
            ['[.-0]', 'any/path', false],
            ['*', '.dotenv', true],
            // escaping
            ['\*\?', '*?', true],
            ['\*\?', 'zz', false],
            ['begin\*\end', 'begin\middle\end', true, ['escape' => false]],
            ['begin\*\end', 'begin\two\steps\end', false, ['escape' => false]],
            ['begin\*\end', 'begin\end', false, ['escape' => false]],
            ['begin\*\end', 'begin\middle\end', true, ['filePath' => true, 'escape' => false]],
            ['begin\*\end', 'begin\two\steps\end', false, ['filePath' => true, 'escape' => false]],
            // ending
            ['i/*.jpg', 'i/hello.jpg', true, ['ending' => true]],
            ['i/*.jpg', 'i/hello.jpg', true, ['ending' => true]],
            ['i/*.jpg', 'i/h/hello.jpg', false, ['ending' => true]],
            ['i/*.jpg', 'i/h/hello.jpg', false, ['ending' => true]],
            ['i/*.jpg', 'path/to/i/hello.jpg', true, ['ending' => true]],
            ['i/*.jpg', 'path/to/i/hello.jpg', true, ['ending' => true]],
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
        if (isset($options['leadingPeriod']) && $options['leadingPeriod'] === true) {
            $wildcardPattern = $wildcardPattern->exactLeadingPeriod();
        }
        if (isset($options['ending']) && $options['ending'] === true) {
            $wildcardPattern = $wildcardPattern->matchEnding();
        }

        return $wildcardPattern;
    }

    public function testDisableOptions(): void
    {
        $wildcardPattern = (new WildcardPattern('abc'))
            ->matchEnding()
            ->matchEnding(false);
        $this->assertFalse($wildcardPattern->match('42abc'));

        $wildcardPattern = (new WildcardPattern('\*42'))
            ->withoutEscape()
            ->withoutEscape(false);
        $this->assertTrue($wildcardPattern->match('*42'));

        $wildcardPattern = (new WildcardPattern('*/42'))
            ->exactLeadingPeriod()
            ->exactLeadingPeriod(false);
        $this->assertTrue($wildcardPattern->match('abc/42'));

        $wildcardPattern = (new WildcardPattern('abc42'))
            ->ignoreCase()
            ->ignoreCase(false);
        $this->assertFalse($wildcardPattern->match('ABC42'));
    }

    public function testImmutability(): void
    {
        $original = new WildcardPattern('*');
        $this->assertNotSame($original, $original->exactLeadingPeriod());
        $this->assertNotSame($original, $original->ignoreCase());
        $this->assertNotSame($original, $original->withoutEscape());
        $this->assertNotSame($original, $original->matchEnding());
    }
}
