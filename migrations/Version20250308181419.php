<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250308181419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discount ADD approved_by_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE discount ADD CONSTRAINT FK_E1E0B40E2D234F6A FOREIGN KEY (approved_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E1E0B40E2D234F6A ON discount (approved_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discount DROP FOREIGN KEY FK_E1E0B40E2D234F6A');
        $this->addSql('DROP INDEX IDX_E1E0B40E2D234F6A ON discount');
        $this->addSql('ALTER TABLE discount DROP approved_by_id');
    }
}
