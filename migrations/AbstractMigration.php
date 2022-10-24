<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration as BaseAbstractMigration;

abstract class AbstractMigration extends BaseAbstractMigration
{
    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform === false,
            "Migration can only be executed safely on 'postgresql'."
        );

        $this->rollback();
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform === false,
            "Migration can only be executed safely on 'postgresql'."
        );

        $this->migrate();
    }

    abstract protected function migrate(): void;

    protected function rollback(): void
    {
        $this->abortIf(true, \sprintf('No rollback() migration implemented for "%s"', static::class));
    }
}
