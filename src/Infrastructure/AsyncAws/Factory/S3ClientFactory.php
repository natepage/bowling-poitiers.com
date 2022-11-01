<?php
declare(strict_types=1);

namespace App\Infrastructure\AsyncAws\Factory;

use AsyncAws\S3\S3Client;

final class S3ClientFactory
{
    public function __construct(private readonly array $config)
    {
    }

    public function create(): S3Client
    {
        return new S3Client($this->config);
    }
}
