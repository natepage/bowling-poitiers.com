<?php
declare(strict_types=1);

use AsyncAws\S3\S3Client;
use Symfony\Config\FlysystemConfig;

return static function (FlysystemConfig $flysystemConfig): void
{
    $flysystemConfig->storage('post_images')
        ->adapter('asyncaws')
        ->options([
            'client' => S3Client::class,
            'bucket' => 'bowling-poitiers-uploads',
            'prefix' => 'images',
        ]);
};
