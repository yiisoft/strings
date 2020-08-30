<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests;

use Yiisoft\Strings\StringHelper;
use PHPUnit\Framework\TestCase;

final class StringHelperTest extends TestCase
{
    public function byteLength(): void
    {
        $this->assertEquals(4, StringHelper::byteLength('this'));
        $this->assertEquals(6, StringHelper::byteLength('это'));
    }

    public function testSubstring(): void
    {
        $this->assertEquals('th', StringHelper::byteSubstring('this', 0, 2));
        $this->assertEquals('э', StringHelper::byteSubstring('это', 0, 2));

        $this->assertEquals('abcdef', StringHelper::byteSubstring('abcdef', 0));
        $this->assertEquals('abcdef', StringHelper::byteSubstring('abcdef', 0, null));

        $this->assertEquals('de', StringHelper::byteSubstring('abcdef', 3, 2));
        $this->assertEquals('def', StringHelper::byteSubstring('abcdef', 3));
        $this->assertEquals('def', StringHelper::byteSubstring('abcdef', 3, null));

        $this->assertEquals('cd', StringHelper::byteSubstring('abcdef', -4, 2));
        $this->assertEquals('cdef', StringHelper::byteSubstring('abcdef', -4));
        $this->assertEquals('cdef', StringHelper::byteSubstring('abcdef', -4, null));

        $this->assertEquals('', StringHelper::byteSubstring('abcdef', 4, 0));
        $this->assertEquals('', StringHelper::byteSubstring('abcdef', -4, 0));

        $this->assertEquals('это', StringHelper::byteSubstring('это', 0));
        $this->assertEquals('это', StringHelper::byteSubstring('это', 0, null));

        $this->assertEquals('т', StringHelper::byteSubstring('это', 2, 2));
        $this->assertEquals('то', StringHelper::byteSubstring('это', 2));
        $this->assertEquals('то', StringHelper::byteSubstring('это', 2, null));

        $this->assertEquals('т', StringHelper::byteSubstring('это', -4, 2));
        $this->assertEquals('то', StringHelper::byteSubstring('это', -4));
        $this->assertEquals('то', StringHelper::byteSubstring('это', -4, null));

        $this->assertEquals('', StringHelper::byteSubstring('это', 4, 0));
        $this->assertEquals('', StringHelper::byteSubstring('это', -4, 0));
    }

    public function testBaseName(): void
    {
        $this->assertEquals('', StringHelper::baseName(''));

        $this->assertEquals('file', StringHelper::baseName('file'));
        $this->assertEquals('file.test', StringHelper::baseName('file.test', '.test2'));
        $this->assertEquals('file', StringHelper::baseName('file.test', '.test'));

        $this->assertEquals('file', StringHelper::baseName('/file'));
        $this->assertEquals('file.test', StringHelper::baseName('/file.test', '.test2'));
        $this->assertEquals('file', StringHelper::baseName('/file.test', '.test'));

        $this->assertEquals('file', StringHelper::baseName('/path/to/file'));
        $this->assertEquals('file.test', StringHelper::baseName('/path/to/file.test', '.test2'));
        $this->assertEquals('file', StringHelper::baseName('/path/to/file.test', '.test'));

        $this->assertEquals('file', StringHelper::baseName('\file'));
        $this->assertEquals('file.test', StringHelper::baseName('\file.test', '.test2'));
        $this->assertEquals('file', StringHelper::baseName('\file.test', '.test'));

        $this->assertEquals('file', StringHelper::baseName('C:\file'));
        $this->assertEquals('file.test', StringHelper::baseName('C:\file.test', '.test2'));
        $this->assertEquals('file', StringHelper::baseName('C:\file.test', '.test'));

        $this->assertEquals('file', StringHelper::baseName('C:\path\to\file'));
        $this->assertEquals('file.test', StringHelper::baseName('C:\path\to\file.test', '.test2'));
        $this->assertEquals('file', StringHelper::baseName('C:\path\to\file.test', '.test'));

        // mixed paths
        $this->assertEquals('file.test', StringHelper::baseName('/path\to/file.test'));
        $this->assertEquals('file.test', StringHelper::baseName('/path/to\file.test'));
        $this->assertEquals('file.test', StringHelper::baseName('\path/to\file.test'));

        // \ and / in suffix
        $this->assertEquals('file', StringHelper::baseName('/path/to/filete/st', 'te/st'));
        $this->assertEquals('st', StringHelper::baseName('/path/to/filete/st', 'te\st'));
        $this->assertEquals('file', StringHelper::baseName('/path/to/filete\st', 'te\st'));
        $this->assertEquals('st', StringHelper::baseName('/path/to/filete\st', 'te/st'));

        // http://www.php.net/manual/en/function.basename.php#72254
        $this->assertEquals('foo', StringHelper::baseName('/bar/foo/'));
        $this->assertEquals('foo', StringHelper::baseName('\\bar\\foo\\'));
    }

    public function testTruncateEnd(): void
    {
        $this->assertEquals('привет, я multibyte…', StringHelper::truncateEnd('привет, я multibyte строка!', 20));
        $this->assertEquals('Не трогаем строку', StringHelper::truncateEnd('Не трогаем строку', 17));
        $this->assertEquals('мы!!!', StringHelper::truncateEnd('мы используем восклицательные знаки', 6, '!!!'));
    }

    public function testTruncateWords(): void
    {
        $this->assertEquals('это тестовая multibyte строка', StringHelper::truncateWords('это тестовая multibyte строка', 5));
        $this->assertEquals('это тестовая multibyte…', StringHelper::truncateWords('это тестовая multibyte строка', 3));
        $this->assertEquals('это тестовая multibyte!!!', StringHelper::truncateWords('это тестовая multibyte строка', 3, '!!!'));
        $this->assertEquals('это строка с          неожиданными…', StringHelper::truncateWords(' это строка с          неожиданными пробелами ', 4));
    }

    /**
     * @dataProvider providerStartsWith
     * @param bool $result
     * @param string $string
     * @param string|null $with
     */
    public function testStartsWith(bool $result, string $string, ?string $with): void
    {
        // case sensitive version check
        $this->assertSame($result, StringHelper::startsWith($string, $with));
        // case insensitive version check
        $this->assertSame($result, StringHelper::startsWith($string, $with, false));
    }

    /**
     * Rules that should work the same for case-sensitive and case-insensitive `startsWith()`.
     */
    public function providerStartsWith(): array
    {
        return [
            // positive check
            [true, '', ''],
            [true, '', null],
            [true, 'string', ''],
            [true, ' string', ' '],
            [true, 'abc', 'abc'],
            [true, 'Bürger', 'Bürger'],
            [true, '我Я multibyte', '我Я'],
            [true, 'Qנטשופ צרכנות', 'Qנ'],
            [true, 'ไทย.idn.icann.org', 'ไ'],
            [true, '!?+', "\x21\x3F"],
            [true, "\x21?+", '!?'],
            // false-positive check
            [false, '', ' '],
            [false, ' ', '  '],
            [false, 'Abc', 'a'],
            [false, 'Abc', 'Abcde'],
            [false, 'abc', 'abe'],
            [false, 'abc', 'b'],
            [false, 'abc', 'c'],
            [false, 'üЯ multibyte', 'Üя multibyte'],
        ];
    }

    public function testStartsWithIgnoringCase(): void
    {
        $this->assertTrue(StringHelper::startsWithIgnoringCase('', ''));
        $this->assertFalse(StringHelper::startsWithIgnoringCase('', ' '));
        $this->assertTrue(StringHelper::startsWithIgnoringCase('sTrInG', 'StRiNg'));
        $this->assertTrue(StringHelper::startsWithIgnoringCase('CaSe', 'cAs'));
        $this->assertTrue(StringHelper::startsWithIgnoringCase('HTTP://BÜrger.DE/', 'http://bürger.de'));
        $this->assertTrue(StringHelper::startsWithIgnoringCase('üЯйΨB', 'ÜяЙΨ'));
        $this->assertTrue(StringHelper::startsWithIgnoringCase('anything', ''));
    }

    /**
     * @dataProvider providerEndsWith
     * @param bool $result
     * @param string $string
     * @param string|null $with
     */
    public function testEndsWith(bool $result, string $string, ?string $with): void
    {
        // case sensitive version check
        $this->assertSame($result, StringHelper::endsWith($string, $with));
    }

    /**
     * Rules that should work the same for case-sensitive and case-insensitive `endsWith()`.
     */
    public function providerEndsWith(): array
    {
        return [
            // positive check
            [true, '', ''],
            [true, '', null],
            [true, 'string', ''],
            [true, 'string ', ' '],
            [true, 'string', 'g'],
            [true, 'abc', 'abc'],
            [true, 'Bürger', 'Bürger'],
            [true, 'Я multibyte строка我!', ' строка我!'],
            [true, '+!?', "\x21\x3F"],
            [true, "+\x21?", "!\x3F"],
            [true, 'נטשופ צרכנות', 'ת'],
            // false-positive check
            [false, '', ' '],
            [false, ' ', '  '],
            [false, 'aaa', 'aaaa'],
            [false, 'abc', 'abe'],
            [false, 'abc', 'a'],
            [false, 'abc', 'b'],
            [false, 'string', 'G'],
            [false, 'multibyte строка', 'А'],
        ];
    }

    public function testEndsWithIgnoringCase(): void
    {
        $this->assertTrue(StringHelper::endsWithIgnoringCase('', ''));
        $this->assertFalse(StringHelper::endsWithIgnoringCase('', ' '));
        $this->assertTrue(StringHelper::endsWithIgnoringCase('sTrInG', 'StRiNg'));
        $this->assertTrue(StringHelper::endsWithIgnoringCase('string', 'nG'));
        $this->assertTrue(StringHelper::endsWithIgnoringCase('BüЯйΨ', 'ÜяЙΨ'));
        $this->assertTrue(StringHelper::endsWithIgnoringCase('anything', ''));
    }

    public function testCountWords(): void
    {
        $this->assertEquals(3, StringHelper::countWords('china 中国 ㄍㄐㄋㄎㄌ'));
        $this->assertEquals(4, StringHelper::countWords('и много тут слов?'));
        $this->assertEquals(4, StringHelper::countWords("и\rмного\r\nтут\nслов?"));
        $this->assertEquals(1, StringHelper::countWords('крем-брюле'));
        $this->assertEquals(1, StringHelper::countWords(' слово '));
    }

    /**
     * @dataProvider base64UrlEncodedStringsProvider
     * @param string $input
     * @param string $base64UrlEncoded
     */
    public function testBase64UrlEncode(string $input, string $base64UrlEncoded): void
    {
        $encoded = StringHelper::base64UrlEncode($input);
        $this->assertEquals($base64UrlEncoded, $encoded);
    }

    /**
     * @dataProvider base64UrlEncodedStringsProvider
     * @param $output
     * @param $base64UrlEncoded
     */
    public function testBase64UrlDecode($output, $base64UrlEncoded): void
    {
        $decoded = StringHelper::base64UrlDecode($base64UrlEncoded);
        $this->assertEquals($output, $decoded);
    }

    public function base64UrlEncodedStringsProvider(): array
    {
        return [
            'Regular string' => ['This is an encoded string', 'VGhpcyBpcyBhbiBlbmNvZGVkIHN0cmluZw=='],
            '? and _ characters' => ['subjects?_d=1', 'c3ViamVjdHM_X2Q9MQ=='],
            '> character' => ['subjects>_d=1', 'c3ViamVjdHM-X2Q9MQ=='],
            'Unicode' => ['Это закодированная строка', '0K3RgtC-INC30LDQutC-0LTQuNGA0L7QstCw0L3QvdCw0Y8g0YHRgtGA0L7QutCw'],
        ];
    }

    public function uppercaseFirstCharacterProvider(): array
    {
        return [
            ['foo', 'Foo'],
            ['foo bar', 'Foo bar'],
            ['👍🏻 foo bar', '👍🏻 foo bar'],
            ['', ''],
            ['здесь我 multibyte我 строка', 'Здесь我 multibyte我 строка'],
        ];
    }

    /**
     * @param string $string
     * @param string $expectedResult
     * @dataProvider uppercaseFirstCharacterProvider
     */
    public function testUppercaseFirstCharacter(string $string, string $expectedResult): void
    {
        $this->assertSame($expectedResult, StringHelper::uppercaseFirstCharacter($string));
    }

    public function uppercaseFirstCharacterInEachWordProvider(): array
    {
        return [
            'Single word' => ['foo', 'Foo'],
            'Multiple words' => ['foo bar', 'Foo Bar'],
            'Unicode smileys' => ['👍🏻 foo bar', '👍🏻 Foo Bar'],
            'Empty' => ['', ''],
            'Unciode' => ['здесь我 multibyte我 строка', 'Здесь我 Multibyte我 Строка'],
        ];
    }

    /**
     * @param string $string
     * @param string $expectedResult
     * @dataProvider uppercaseFirstCharacterInEachWordProvider
     */
    public function testUppercaseFirstCharacterInEachWord(string $string, string $expectedResult): void
    {
        $this->assertSame($expectedResult, StringHelper::uppercaseFirstCharacterInEachWord($string));
    }

    public function testTruncateBegin(): void
    {
        $this->assertSame('…56', StringHelper::truncateBegin('123456', 3));
        $this->assertSame('*456', StringHelper::truncateBegin('123456', 4, '*'));
        $this->assertSame('123456', StringHelper::truncateBegin('123456', 6));
        $this->assertSame('…ет', StringHelper::truncateBegin('привет', 3));
    }

    public function testTruncateMiddle(): void
    {
        $this->assertSame('Hell…r 2', StringHelper::truncateMiddle('Hello world number 2', 8));
        $this->assertSame('Hell***r 2', StringHelper::truncateMiddle('Hello world number 2', 10, '***'));
        $this->assertSame('Ответ на…о такого', StringHelper::truncateMiddle('Ответ на главный вопрос жизни, вселенной и всего такого', 17));
    }

    public function testTruncateMiddleWithLengthGreaterThanString(): void
    {
        $this->assertSame('Hello world', StringHelper::truncateMiddle('Hello world', 11, '*'));
    }

    public function testDirectoryName(): void
    {
        $this->assertSame('\App\Test', StringHelper::directoryName('\App\Test\Class.php'));
        $this->assertSame('', StringHelper::directoryName('Class.php'));
    }

    public function testUppercase(): void
    {
        $this->assertSame('UPPER', StringHelper::uppercase('uPpEr'));
        $this->assertSame('ВЫШЕ', StringHelper::uppercase('вЫшЕ'));
    }

    public function testLowercase(): void
    {
        $this->assertSame('lower', StringHelper::lowercase('LoWeR'));
        $this->assertSame('ниже', StringHelper::lowercase('НиЖе'));
    }

    public function testLength(): void
    {
        $this->assertSame(8, StringHelper::length('a string'));
        $this->assertSame(3, StringHelper::length('три'));
    }

    /**
     * @see https://github.com/php/php-src/blob/master/ext/standard/tests/strings/substr_replace.phpt
     */
    public function replaceSubstringProvider(): array
    {
        return [
            ['trbala ', ['try this', 'bala ', 2]],
            ['trbala his', ['try this', 'bala ', 2, 3]],
            ['trbala is', ['try this', 'bala ', 2, -2]],
            ['bala ', ['try this', 'bala ', -10]],
            ['try thisbala ', ['try this', 'bala ', 8]],
            ['строфа', ['строка', 'офа', -3]],
        ];
    }

    /**
     * @dataProvider replaceSubstringProvider
     */
    public function testReplaceSubstring(string $expected, array $arguments): void
    {
        $this->assertSame($expected, StringHelper::replaceSubstring(...$arguments));
    }
}
