<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250308131910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE discount (id CHAR(36) NOT NULL, target_project_id CHAR(36) DEFAULT NULL, created_by_id CHAR(36) NOT NULL, code VARCHAR(50) NOT NULL, amount INT NOT NULL, is_percentage TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, created_on DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_on DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_E1E0B40E77153098 (code), INDEX IDX_E1E0B40E2481C70D (target_project_id), INDEX IDX_E1E0B40EB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE discount ADD CONSTRAINT FK_E1E0B40E2481C70D FOREIGN KEY (target_project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE discount ADD CONSTRAINT FK_E1E0B40EB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discount DROP FOREIGN KEY FK_E1E0B40E2481C70D');
        $this->addSql('ALTER TABLE discount DROP FOREIGN KEY FK_E1E0B40EB03A8386');
        $this->addSql('DROP TABLE discount');
    }
}
