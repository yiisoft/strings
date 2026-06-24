<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests\Benchmark;

use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use Yiisoft\Strings\StringHelper;

/**
 * @Iterations(10)
 * @Revs(1000)
 * @Warmup(2)
 */
final class StringHelperBench
{
    public function benchByteLength(): void
    {
        StringHelper::byteLength('Hello, world!');
    }

    public function benchByteSubstring(): void
    {
        StringHelper::byteSubstring('Hello, world!', 7, 5);
    }

    public function benchBaseName(): void
    {
        StringHelper::baseName('/path/to/file.php', '.php');
    }

    public function benchDirectoryName(): void
    {
        StringHelper::directoryName('/path/to/file.php');
    }

    public function benchSubstring(): void
    {
        StringHelper::substring('Hello, world!', 7, 5);
    }

    public function benchReplaceSubstring(): void
    {
        StringHelper::replaceSubstring('Hello, world!', 'planet', 7, 5);
    }

    public function benchStartsWith(): void
    {
        StringHelper::startsWith('Hello, world!', 'Hello');
    }

    public function benchStartsWithIgnoringCase(): void
    {
        StringHelper::startsWithIgnoringCase('Hello, world!', 'hello');
    }

    public function benchEndsWith(): void
    {
        StringHelper::endsWith('Hello, world!', 'world!');
    }

    public function benchEndsWithIgnoringCase(): void
    {
        StringHelper::endsWithIgnoringCase('Hello, world!', 'World!');
    }

    public function benchTruncateBegin(): void
    {
        StringHelper::truncateBegin('Hello, world!', 10);
    }

    public function benchTruncateMiddle(): void
    {
        StringHelper::truncateMiddle('Hello, world!', 10);
    }

    public function benchTruncateEnd(): void
    {
        StringHelper::truncateEnd('Hello, world!', 10);
    }

    public function benchSplit(): void
    {
        StringHelper::split('Hello, world!', ' ');
    }

    public function benchTrim(): void
    {
        StringHelper::trim('  Hello, world!  ');
    }

    public function benchFindBetween(): void
    {
        StringHelper::findBetween('<a>b</a>', '<a>', '</a>');
    }

    public function benchMatchAnyRegex(): void
    {
        StringHelper::matchAnyRegex('string', ['/[a-z]+/', '/[0-9]+/']);
    }
}
