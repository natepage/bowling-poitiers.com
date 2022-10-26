<?php
declare(strict_types=1);

namespace DoctrineMigrations;

final class Version20221026135151 extends AbstractMigration
{
    public function migrate(): void
    {
        $this->addSql('ALTER TABLE post ADD slug VARCHAR(255) NOT NULL');
    }

    public function rollback(): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE post DROP slug');
    }
}
