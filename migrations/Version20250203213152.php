<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203213152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certification (id CHAR(36) NOT NULL, authority_id CHAR(36) DEFAULT NULL, project_id CHAR(36) DEFAULT NULL, name VARCHAR(100) NOT NULL, created_on DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_on DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_6C3C6D7581EC865B (authority_id), INDEX IDX_6C3C6D75166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D7581EC865B FOREIGN KEY (authority_id) REFERENCES certification_authority (id)');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D7581EC865B');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75166D1F9C');
        $this->addSql('DROP TABLE certification');
    }
}
