<?php
declare(strict_types=1);

use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

/** @var string $env */

return static function (FrameworkConfig $frameworkConfig) use ($env): void {
    $frameworkConfig
        ->router()
        ->utf8(true);

    if ($env === 'prod') {
        $frameworkConfig
            ->router()
            ->strictRequirements(null);
    }
};
