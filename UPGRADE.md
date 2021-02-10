# Upgrading Instructions

These notes highlight changes that could break your application when you upgrade package from one version to another.

Upgrading in general is as simple as updating your dependency in your `composer.json` and running `composer update`.
In a big application however there may be more things to consider, which are explained in the following.

> Note: The following upgrading instructions are cumulative. That is, if you want to upgrade from a version A to version
> C and there is a version B between A and C, you need to follow the instructions for both A and B.

## Upgrade from 1.2.0

`\Yiisoft\Strings\WildCardPattern` behavior was changed:

- `*` now doesn't match `/`.
- `**` was introduced to match anything including `/`.
- `\Yiisoft\Strings\WildcardPattern#withExactSlashes()` was removed.
- `\Yiisoft\Strings\WildcardPattern#withEnding()` was removed.

Remove `withExactSlashes()` calls. Replace `*` with `**` in patterns if you need to match `/` as well.
If `withEnding()` was used, add `**` to the beginning of the pattern.
  
There are two method name adjustments:

- `\Yiisoft\Strings\WildcardPattern#withExactLeadingPeriod()` was renamed to `Yiisoft\Strings\WildcardPattern#exactLeadingPeriod()`.

Adjust methods usage accordingly.
