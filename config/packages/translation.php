<?php
declare(strict_types=1);

use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (FrameworkConfig $frameworkConfig): void {
    $frameworkConfig->defaultLocale('en');

    $frameworkConfig->translator()
        ->enabled(true)
        ->defaultPath(param('kernel.project_dir') . '/translations')
        ->fallbacks(['en']);
};
