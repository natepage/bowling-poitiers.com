<?php
declare(strict_types=1);


use Symfony\Config\VichUploaderConfig;
use Vich\UploaderBundle\Naming\SmartUniqueNamer;

return static function (VichUploaderConfig $vichUploaderConfig): void {
    $vichUploaderConfig
        ->dbDriver('orm')
        ->storage('flysystem');

    $vichUploaderConfig->metadata()
        ->type('attribute');

    $vichUploaderConfig->mappings('post_images')
        ->uriPrefix('/images/posts')
        ->uploadDestination('post_images')
        ->namer(SmartUniqueNamer::class);
};
