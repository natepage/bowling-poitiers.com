<?php
declare(strict_types=1);

namespace DoctrineMigrations;

final class Version20221027122353 extends AbstractMigration
{
    public function migrate(): void
    {
        $this->addSql('CREATE TABLE post (id UUID NOT NULL, content TEXT NOT NULL, description VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN post.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN post.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function rollback(): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE post');
    }
}
