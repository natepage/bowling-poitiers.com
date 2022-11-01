<?php
declare(strict_types=1);

/** @var string $env */

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

/** @var string $env */

return static function (ContainerConfigurator $container, FrameworkConfig $frameworkConfig) use ($env): void {
    if ($env === 'dev') {
        $container->extension('web_profiler', [
            'toolbar' => true,
            'intercept_redirects' => false,
        ]);

        $frameworkConfig->profiler()
            ->onlyExceptions(false)
            ->collectSerializerData(true);
    }

    if ($env === 'test') {
        $container->extension('web_profiler', [
            'toolbar' => false,
            'intercept_redirects' => false,
        ]);

        $frameworkConfig->profiler()
            ->collect(false);
    }
};
