# Yii Strings Change Log

## 2.1.0 August 20, 2022

- New #75: Add method `Inflector::toSnakeCase()` that convert word to "snake_case" (@soodssr)
- New #81: Add `StringHelper::parsePath()` method (@arogachev, @vjik)

## 2.0.0 February 10, 2021

- Chg #67: Remove `\Yiisoft\Strings\WildcardPattern::withoutEscape()` (@samdark)
- Chg #67: Remove `\Yiisoft\Strings\WildcardPattern::withExactLeadingPeriod()` (@samdark)
- Enh #67: Add `**`, match anything including `/`, to `\Yiisoft\Strings\WildcardPattern`, remove `withExactSlashes()` and `withEnding()` (@samdark)
- Enh #67: Allow specifying delimiters for `*` (@samdark)
- Enh #67: Add `\Yiisoft\Strings\WildcardPattern::isDynamic()` (@samdark)
- Enh #67: Add `\Yiisoft\Strings\WildcardPattern::quote()` (@samdark)

## 1.2.0 January 22, 2021

- Enh #62: Add method `StringHelper::split()` that split a string to array with non-empty lines (@vjik)
- Enh #63: Add method `NumericHelper::isInteger()` that checks whether the given string is an integer number (@vjik)
- Enh #64: Add support of a boolean values to `NumericHelper::normalize()` (@vjik)

## 1.1.0 November 13, 2020

- Enh #52: Allow turning off options in `WildcardPattern` (@vjik)
- Enh #51: Add an option `withEnding()` to `WildcardPattern` for match ending of testing string (@vjik)
- Bug #44: `NumericHelper::toOrdinal()` throws an error for numbers with fractional part (@vjik)

## 1.0.1 October 12, 2020

- Enh #40: Use `str_starts_with()` and `str_ends_with()` if available (@viktorprogger)
- Bug #43: `NumericHelper::normalize()` throws an error for `float` or `int` values in PHP 8 (@vjik)

## 1.0.0 August 31, 2020

- Initial release.
