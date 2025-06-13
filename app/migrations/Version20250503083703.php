<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250503083703 extends AbstractMigration
{

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE texts (
                id UUID NOT NULL,
                image_id UUID DEFAULT NULL,
                text TEXT NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
               )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_1E3513BF3DA5256D ON texts (image_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN texts.id IS '(DC2Type:ulid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN texts.image_id IS '(DC2Type:ulid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN texts.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN texts.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE texts ADD CONSTRAINT FK_1E3513BF3DA5256D FOREIGN KEY (image_id) REFERENCES images (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE texts DROP CONSTRAINT FK_1E3513BF3DA5256D
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE texts
        SQL);
    }
}
