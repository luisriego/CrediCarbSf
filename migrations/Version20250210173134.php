<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210173134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certification_type_entity (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(1500) NOT NULL, UNIQUE INDEX UNIQ_9C73A0735E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping_cart (id CHAR(36) NOT NULL, total NUMERIC(10, 2) NOT NULL, tax NUMERIC(10, 2) NOT NULL, status VARCHAR(255) NOT NULL, created_on DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_on DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping_cart_item (id CHAR(36) NOT NULL, project_id CHAR(36) DEFAULT NULL, quantity INT NOT NULL, price NUMERIC(10, 2) NOT NULL, created_on DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_on DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_E59A1DF4166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shopping_cart_item ADD CONSTRAINT FK_E59A1DF4166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75166D1F9C');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D7581EC865B');
        $this->addSql('DROP INDEX IDX_6C3C6D7581EC865B ON certification');
        $this->addSql('DROP INDEX IDX_6C3C6D75166D1F9C ON certification');
        $this->addSql('ALTER TABLE certification ADD description VARCHAR(255) DEFAULT NULL, DROP authority_id, DROP project_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_cart_item DROP FOREIGN KEY FK_E59A1DF4166D1F9C');
        $this->addSql('DROP TABLE certification_type_entity');
        $this->addSql('DROP TABLE shopping_cart');
        $this->addSql('DROP TABLE shopping_cart_item');
        $this->addSql('ALTER TABLE certification ADD authority_id CHAR(36) DEFAULT NULL, ADD project_id CHAR(36) DEFAULT NULL, DROP description');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D7581EC865B FOREIGN KEY (authority_id) REFERENCES certification_authority (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6C3C6D7581EC865B ON certification (authority_id)');
        $this->addSql('CREATE INDEX IDX_6C3C6D75166D1F9C ON certification (project_id)');
    }
}
