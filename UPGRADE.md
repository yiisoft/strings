# Upgrading Instructions for Yii Strings

This file contains the upgrade notes. These notes highlight changes that could break your
application when you upgrade the package from one version to another.

> **Important!** The following upgrading instructions are cumulative. That is, if you want
> to upgrade from version A to version C and there is version B between A and C, you need
> to following the instructions for both A and B.

## Upgrade from 2.x to 3.x

Default value of `$strict` parameter in `Inflector::toSnakeCase()` changed to `false`. To keep previous behavior, pass `strict: true` when calling `toSnakeCase()` without explicitly specifying the `$strict` argument. For example:

    $inflector = new Inflector();
    $inflector->toSnakeCase($name);
    // change to
    $inflector->toSnakeCase($name, strict: true);

## Upgrade from 1.x to 2.x

`\Yiisoft\Strings\WildCardPattern` was changed.

- `\Yiisoft\Strings\WildcardPattern::withExactSlashes()` was removed. `*` now always doesn't match `/`.
- `**` was introduced to match anything including `/`.
- `\Yiisoft\Strings\WildcardPattern::withExactLeadingPeriod()` was removed. There is no replacement for old behavior.
- `\Yiisoft\Strings\WildcardPattern::withEnding()` was removed.
- `\Yiisoft\Strings\WildcardPattern::withoutEscape()` was removed.  

To fix possible issues:

- Remove `withExactSlashes()` calls.
- Replace `*` with `**` in patterns if you need to match `/` as well.
- If `withEnding()` was used, add `**` to the beginning of the pattern.
- If `withoutEscape()` was used, escape `\` in patterns with another `\`.
  Likely `\Yiisoft\Strings\WildcardPattern::quote()` may be of help.
