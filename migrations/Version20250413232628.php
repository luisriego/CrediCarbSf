<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413232628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE budget_expenses (id CHAR(36) NOT NULL, project_id CHAR(36) DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, category VARCHAR(100) NOT NULL, status VARCHAR(20) NOT NULL, notes LONGTEXT DEFAULT NULL, receipt_url VARCHAR(255) DEFAULT NULL, tags VARCHAR(255) DEFAULT NULL, completed_at DATETIME DEFAULT NULL, created_on DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_on DATETIME NOT NULL, budgeted_amount_in_cents INT NOT NULL, budgeted_currency VARCHAR(3) NOT NULL, actual_amount_in_cents INT NOT NULL, actual_currency VARCHAR(3) NOT NULL, INDEX IDX_F359A2FF166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE budget_expenses ADD CONSTRAINT FK_F359A2FF166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE company CHANGE fantasy_name fantasy_name VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE budget_expenses DROP FOREIGN KEY FK_F359A2FF166D1F9C');
        $this->addSql('DROP TABLE budget_expenses');
        $this->addSql('ALTER TABLE company CHANGE fantasy_name fantasy_name VARCHAR(100) DEFAULT NULL');
    }
}
