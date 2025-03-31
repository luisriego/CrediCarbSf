<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250330203419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discount CHANGE expires_at expires_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE project ADD quantity_in_tons INT NOT NULL, ADD price_in_cents INT NOT NULL, DROP quantity, DROP price, CHANGE area_ha area_ha INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discount CHANGE expires_at expires_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD quantity NUMERIC(10, 2) NOT NULL, ADD price NUMERIC(10, 2) NOT NULL, DROP quantity_in_tons, DROP price_in_cents, CHANGE area_ha area_ha NUMERIC(10, 2) NOT NULL');
    }
}
