<?php
declare(strict_types=1);

/** @var string $env */

use Symfony\Config\FrameworkConfig;
use Symfony\Config\WebProfilerConfig;

/** @var string $env */

return static function (FrameworkConfig $frameworkConfig, WebProfilerConfig $webProfilerConfig) use ($env): void {
    if ($env === 'dev') {
        $webProfilerConfig
            ->toolbar(true)
            ->interceptRedirects(false);

        $frameworkConfig->profiler()
            ->onlyExceptions(false)
            ->collectSerializerData(true);
    }

    if ($env === 'test') {
        $webProfilerConfig
            ->toolbar(false)
            ->interceptRedirects(false);

        $frameworkConfig->profiler()
            ->collect(false);
    }
};
