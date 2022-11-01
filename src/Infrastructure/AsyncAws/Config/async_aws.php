<?php
declare(strict_types=1);

use App\Infrastructure\AsyncAws\Factory\S3ClientFactory;
use AsyncAws\S3\S3Client;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()
        ->autoconfigure()
        ->autowire();

    $services
        ->set(S3ClientFactory::class)
        ->arg('$config', [
            'accessKeyId' => env('AWS_S3_ACCESS_KEY_ID'),
            'accessKeySecret' => env('AWS_S3_ACCESS_KEY_SECRET'),
            'region' => 'ap-southeast-2',
        ]);

    $services
        ->set(S3Client::class)
        ->factory([service(S3ClientFactory::class), 'create']);
};
