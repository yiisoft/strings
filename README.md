<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
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

## Installation

```
composer require yiisoft/strings
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

## NumericHelper usage

Numeric helper methods are static so usage is like the following:

```php
echo \Yiisoft\Strings\NumericHelper::toOrdinal(3); // 3rd
```

The following methods are available:

- toOrdinal
- normalize

## Inflector usage

```php
echo (new \Yiisoft\Strings\Inflector())->withoutIntl()->toSlug('Strings are cool!'); // strings-are-cool
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
- `*` matches any string, including the empty string.
- `?` matches any single character.
- `[seq]` matches any character in seq.
- `[a-z]` matches any character from a to z.
- `[!seq]` matches any character not in seq.
- `[[:alnum:]]` matches [POSIX style character classes](https://www.php.net/manual/en/regexp.reference.character-classes.php).

Several options are available. Call these before doing a `match()`:

- `withoutEscape()` - makes `\` a regular character in a pattern.
- `withExactSlashes()` - makes `\` in a string to match `\` only in a pattern. 
- `ignoreCase()` - case-insensitive match.
- `withExactLeadingPeriod()` - makes first `.` in a string match only `.` in a pattern.

When matching file paths, it is advised to use both `withExactSlashes()` and `withExactLeadingPeriod()`:

```php
use \Yiisoft\Strings\WildcardPattern;

$startsWithTest = (new WildcardPattern('config/*.php'))
    ->withExactLeadingPeriod()
    ->withExactSlashes();

if ($startsWithTest->match($fileName)) {
    echo 'It is a config!';
}
```
