<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->load('App\\', '../src/*')
        ->exclude([
            '../src/DependencyInjection/',
            '../src/Entity/',
            '../src/Kernel.php',
            '../src/**/Config/*.php',
        ]);

    $services
        ->load('App\\Admin\\Controller\\', '../src/Admin/Controller/*')
        ->tag('controller.service_arguments');
};
