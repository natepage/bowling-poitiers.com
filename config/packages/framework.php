<?php
declare(strict_types=1);

use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

/** @var string $env */

return static function (FrameworkConfig $frameworkConfig) use ($env): void {
    $frameworkConfig
        ->secret(env('APP_SECRET'))
        ->httpMethodOverride(false);

    $frameworkConfig
        ->phpErrors()
        ->log();

    $frameworkConfig
        ->session()
        ->handlerId(null)
        ->cookieSecure('auto')
        ->cookieSamesite('lax')
        ->storageFactoryId('session.storage.factory.native');

    if ($env === 'test') {
        $frameworkConfig->test(true);

        $frameworkConfig
            ->session()
            ->storageFactoryId('session.storage.factory.mock_file');
    }
};
