<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103214631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode ADD audio VARCHAR(255) DEFAULT NULL, ADD cover_image VARCHAR(255) DEFAULT NULL, DROP audio_url, DROP cover_image_url');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode ADD audio_url VARCHAR(255) DEFAULT NULL, ADD cover_image_url VARCHAR(255) DEFAULT NULL, DROP audio, DROP cover_image');
    }
}
