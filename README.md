<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii Strings</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/strings/v/stable.png)](https://packagist.org/packages/yiisoft/strings)
[![Total Downloads](https://poser.pugx.org/yiisoft/strings/downloads.png)](https://packagist.org/packages/yiisoft/strings)
[![Build Status](https://github.com/yiisoft/strings/workflows/build/badge.svg)](https://github.com/yiisoft/strings/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/strings/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/strings/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/strings/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/strings/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fstrings%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/strings/master)
[![static analysis](https://github.com/yiisoft/strings/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/strings/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/strings/coverage.svg)](https://shepherd.dev/github/yiisoft/strings)

The package provides:

- `StringHelper` that has static methods to work with strings;
- `NumericHelper` that has static methods to work with numeric strings;
- `Inflector` provides methods such as `toPlural()` or `toSlug()` that derive a new string based on the string given;
- `WildcardPattern` is a shell wildcard pattern to match strings against.

## Requirements

- PHP 7.4 or higher.

## Installation

```
composer require yiisoft/strings --prefer-dist
```

## StringHelper usage

String helper methods are static so usage is like the following:

```php
echo \Yiisoft\Strings\StringHelper::countWords('Strings are cool!'); // 3
```

Overall the helper has the following method groups.

### Bytes

- byteLength
- byteSubstring

### File paths

- baseName
- directoryName

### Substrings

- substring
- replaceSubstring
- startsWith
- startsWithIgnoringCase
- endsWith
- endsWithIgnoringCase

### Truncation

- truncateBegin
- truncateMiddle
- truncateEnd
- truncateWords

### Counting

- length
- countWords

### Lowercase and uppercase

- lowercase
- uppercase
- uppercaseFirstCharacter
- uppercaseFirstCharacterInEachWord

### URL friendly base64

- base64UrlEncode
- base64UrlDecode

### Other

- parsePath
- split

## NumericHelper usage

Numeric helper methods are static so usage is like the following:

```php
echo \Yiisoft\Strings\NumericHelper::toOrdinal(3); // 3rd
```

The following methods are available:

- toOrdinal
- normalize
- isInteger

## Inflector usage

```php
echo (new \Yiisoft\Strings\Inflector())
    ->withoutIntl()
    ->toSlug('Strings are cool!'); // strings-are-cool
```

Overall the inflector has the following method groups.

### Plurals and singulars

- toPlural
- toSingular

### Transliteration

- toTransliterated

### Case conversion

- pascalCaseToId
- toPascalCase
- toCamelCase

### Words and sentences

- toSentence
- toWords
- toHumanReadable

### Classes and database tables

- classToTable
- tableToClass

### URLs

- toSlug

## WildcardPattern usage

`WildcardPattern` allows a simple POSIX-style string matching. 

```php
use \Yiisoft\Strings\WildcardPattern;

$startsWithTest = new WildcardPattern('test*');
if ($startsWithTest->match('testIfThisIsTrue')) {
    echo 'It starts with "test"!';
}
```

The following characters are special in the pattern:

- `\` escapes other special characters if usage of escape character is not turned off.
- `*` matches any string, including the empty string, except delimiters (`/` and `\` by default).
- `**` matches any string, including the empty string and delimiters.
- `?` matches any single character.
- `[seq]` matches any character in seq.
- `[a-z]` matches any character from a to z.
- `[!seq]` matches any character not in seq.
- `[[:alnum:]]` matches [POSIX style character classes](https://www.php.net/manual/en/regexp.reference.character-classes.php).

`ignoreCase()` could be called before doing a `match()` to get a case-insensitive match:

```php
use \Yiisoft\Strings\WildcardPattern;

$startsWithTest = new WildcardPattern('test*');
if ($startsWithTest
    ->ignoreCase()
    ->match('tEStIfThisIsTrue')) {
    echo 'It starts with "test"!';
}
```

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework. To run it:

```shell
./vendor/bin/infection
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

## License

The Yii Strings is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
