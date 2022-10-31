<?php
declare(strict_types=1);

namespace DoctrineMigrations;

final class Version20221031061808 extends AbstractMigration
{
    public function migrate(): void
    {
        $this->addSql('CREATE TABLE post (id UUID NOT NULL, content TEXT NOT NULL, description VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN post.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN post.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE post_image (id UUID NOT NULL, post_id UUID DEFAULT NULL, filename VARCHAR(255) NOT NULL, filesize INT NOT NULL, mime_type VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_522688B04B89032C ON post_image (post_id)');
        $this->addSql('COMMENT ON COLUMN post_image.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN post_image.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post_image ADD CONSTRAINT FK_522688B04B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function rollback(): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE post_image DROP CONSTRAINT FK_522688B04B89032C');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_image');
    }
}
