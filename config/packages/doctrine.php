<?php
declare(strict_types=1);

use App\Infrastructure\Doctrine\Dbal\Type\CarbonImmutableType;
use Doctrine\DBAL\Types\Types;
use Symfony\Config\DoctrineConfig;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

/** @var string $env */

return static function (DoctrineConfig $doctrineConfig, FrameworkConfig $frameworkConfig) use ($env): void {
    $doctrineConfig->dbal()
        ->defaultConnection('default')
        ->connection('default')
        ->url(env('DATABASE_URL')->resolve());

    $doctrineConfig->dbal()
        ->type(Types::DATETIME_IMMUTABLE, CarbonImmutableType::class);

    $doctrineConfig->orm()
        ->autoGenerateProxyClasses(true)
        ->defaultEntityManager('default');

    $em = $doctrineConfig->orm()
        ->entityManager('default')
        ->namingStrategy('doctrine.orm.naming_strategy.underscore_number_aware')
        ->autoMapping(true);

    $em->mapping('App\Entity')
        ->isBundle(false)
        ->dir(param('kernel.project_dir') . '/src/Entity')
        ->prefix('App\Entity')
        ->alias('App');

    if ($env === 'test') {
        $doctrineConfig->dbal()
            ->connection('default')
            ->dbnameSuffix('_test' . env('TEST_TOKEN')->default(''));
    }

    if ($env === 'prod') {
        $doctrineConfig->orm()
            ->autoGenerateProxyClasses(false);

        $em->queryCacheDriver()
            ->type('pool')
            ->pool('doctrine.system_cache_pool');

        $em->resultCacheDriver()
            ->type('pool')
            ->pool('doctrine.result_cache_pool');

        $frameworkConfig->cache()
            ->pool('doctrine.result_cache_pool')
            ->adapters(['cache.app']);

        $frameworkConfig->cache()
            ->pool('doctrine.system_cache_pool')
            ->adapters(['cache.system']);
    }
};
