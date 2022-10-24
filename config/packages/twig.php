<?php
declare(strict_types=1);

use Symfony\Config\TwigConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

/** @var string $env */

return static function (TwigConfig $twigConfig) use ($env): void {
    $twigConfig->defaultPath(param('kernel.project_dir') . '/templates');

    if ($env === 'test') {
        $twigConfig->strictVariables(true);
    }
};
