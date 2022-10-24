<?php
declare(strict_types=1);

use Symfony\Config\DoctrineMigrationsConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (DoctrineMigrationsConfig $doctrineMigrationsConfig): void {
    $doctrineMigrationsConfig
        ->enableProfiler(false)
        ->customTemplate(param('kernel.project_dir') . 'migrations/migration.tpl')
        ->migrationsPath('DoctrineMigrations', param('kernel.project_dir') . '/src/migrations');
};
