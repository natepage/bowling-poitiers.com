<?php
declare(strict_types=1);

namespace App\Infrastructure\AsyncAws\Factory;

use AsyncAws\S3\S3Client;

final class S3ClientFactory
{
    public function create(): S3Client
    {
        return new S3Client([
            'accessKeyId' => 'AKIA5KDI4VIJD7RNKFDZ',
            'accessKeySecret' => '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',
            'region' => 'ap-southeast-2',
        ]);
    }
}
