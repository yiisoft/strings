<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
    ->disableComposerAutoloadPathScan()
    ->setFileExtensions(['php'])
    ->addPathToScan(__DIR__ . '/src', isDev: false)
    ->addPathToScan(__DIR__ . '/tests', isDev: true)
    // ext-intl is used conditionally (guarded by extension_loaded('intl')), so it's not a hard requirement.
    ->ignoreErrorsOnExtensions(['ext-intl'], [ErrorType::SHADOW_DEPENDENCY])
    // ext-filter is optional dependency
    ->ignoreErrorsOnExtensions(['ext-filter'], [ErrorType::SHADOW_DEPENDENCY]);
