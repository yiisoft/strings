<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii Strings</h1>
    <br>
</p>

The Yii string helper provides:

- StringHelper that has static methods allowing dealing with strings more efficiently;
- Inflector provides methods such as `pluralize()` or `slug()` that derive a new string based on the string given.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/strings/v/stable.png)](https://packagist.org/packages/yiisoft/strings)
[![Total Downloads](https://poser.pugx.org/yiisoft/strings/downloads.png)](https://packagist.org/packages/yiisoft/strings)
[![Build Status](https://github.com/yiisoft/strings/workflows/build/badge.svg)](https://github.com/yiisoft/strings/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/strings/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/strings/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/strings/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/strings/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fstrings%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/strings/master)
[![static analysis](https://github.com/yiisoft/strings/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/strings/actions?query=workflow%3A%22static+analysis%22)

## Installation

```
composer require yiisoft/strings
```

## General usage

StringHelper methods are static so usage is like the following:

```php
echo \Yiisoft\Strings\StringHelper::countWords('Strings are cool!'); // 3
```

Inflector usage is the following:

```php
echo (new \Yiisoft\Strings\Inflector())->withoutIntl()->slug('Strings are cool!'); // strings-are-cool
```

Check both classes to find out about the methods available.
