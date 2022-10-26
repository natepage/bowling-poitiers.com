<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Config\SecurityConfig;

/** @var string $env */

return static function (SecurityConfig $securityConfig, ContainerConfigurator $containerConfigurator) use ($env): void {
    $securityConfig
        ->passwordHasher(PasswordAuthenticatedUserInterface::class)
        ->algorithm('auto');

    if ($env === 'test') {
        $securityConfig
            ->passwordHasher(PasswordAuthenticatedUserInterface::class)
            ->cost(4)
            ->timeCost(3)
            ->memoryCost(10);
    }

    $securityConfig
        ->provider('users_in_memory')
        ->memory();

    $securityConfig->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false);

    $securityConfig->firewall('main')
        ->lazy(true)
        ->provider('users_in_memory');

    //$securityConfig->accessControl();
};
