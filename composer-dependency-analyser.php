<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
    ->disableComposerAutoloadPathScan()
    ->setFileExtensions(['php'])
    ->addPathToScan(__DIR__ . '/src', isDev: false)
    ->addPathToScan(__DIR__ . '/tests', isDev: true)
    // ext-intl is used conditionally (guarded by extension_loaded('intl')) and ext-filter ships with PHP
    // core by default, so neither is a hard requirement. Previously whitelisted in composer-require-checker.json.
    ->ignoreErrorsOnExtensions(['ext-intl', 'ext-filter'], [ErrorType::SHADOW_DEPENDENCY]);
